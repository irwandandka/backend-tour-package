<?php

namespace Tests\Feature;

use App\Events\TransactionOrdered;
use App\Events\TransactionPaid;
use App\Models\Currency;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductPrice;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.internal.api_key' => 'test-api-key']);
    }

    private function headers(User $user, array $extra = []): array
    {
        $token = $user->createToken('test')->plainTextToken;

        return array_merge([
            'X-API-KEY' => 'test-api-key',
            'Authorization' => 'Bearer ' . $token,
        ], $extra);
    }

    private function makeBookableProduct(): array
    {
        $currency = Currency::factory()->create();
        $product = Product::factory()->create([
            'purchase_currency_id' => $currency->id,
            'sales_currency_id' => $currency->id,
        ]);
        $detail = ProductDetail::factory()->create(['product_id' => $product->id]);
        ProductPrice::factory()->create(['product_detail_id' => $detail->id, 'level' => 1]);

        return [$product, $detail, $currency];
    }

    private function makeTransactionFor(User $owner, ?string $statusId = null): Transaction
    {
        [$product, , $currency] = $this->makeBookableProduct();

        return Transaction::factory()->create([
            'user_id' => $owner->id,
            'product_id' => $product->id,
            'currency_id' => $currency->id,
            'status_id' => $statusId ?? Status::factory()->create(['code' => 'entry'])->id,
        ]);
    }

    public function test_list_returns_only_active_payment_methods(): void
    {
        $user = User::factory()->create();
        PaymentMethod::factory()->create(['is_active' => true, 'name' => 'Gopay']);
        PaymentMethod::factory()->create(['is_active' => false, 'name' => 'Disabled']);

        $response = $this->withHeaders($this->headers($user))->getJson('/api/v1/payment/list');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_set_payment_method_succeeds_for_own_transaction(): void
    {
        // the TransactionOrdered listener does heavy unrelated side effects
        // (PDF invoice generation, email, Telegram) that don't belong in a
        // test of "does the payment method get associated correctly".
        // Scoped to this event only — a blanket Event::fake() also silences
        // Eloquent's internal model events, breaking the UUID-generating
        // creating() hook on every model's boot() method.
        Event::fake([TransactionOrdered::class]);

        $user = User::factory()->create();
        $entry = Status::factory()->create(['code' => 'entry']);
        Status::factory()->create(['code' => 'pending']);
        $paymentMethod = PaymentMethod::factory()->create(['is_active' => true]);
        $transaction = $this->makeTransactionFor($user, $entry->id);

        $response = $this->withHeaders($this->headers($user))->postJson('/api/v1/payment/set-payment-method', [
            'transaction_id' => $transaction->id,
            'payment_method' => $paymentMethod->id,
        ]);

        $response->assertOk();
        $fresh = $transaction->fresh();
        $this->assertSame($paymentMethod->id, $fresh->payment_method_id);
        $this->assertSame('pending', $fresh->status->code);
    }

    public function test_set_payment_method_rejects_when_transaction_already_paid(): void
    {
        $user = User::factory()->create();
        Status::factory()->create(['id' => Status::STATUS_PAID, 'code' => 'paid']);
        $paymentMethod = PaymentMethod::factory()->create(['is_active' => true]);
        $transaction = $this->makeTransactionFor($user, Status::STATUS_PAID);

        $response = $this->withHeaders($this->headers($user))->postJson('/api/v1/payment/set-payment-method', [
            'transaction_id' => $transaction->id,
            'payment_method' => $paymentMethod->id,
        ]);

        $response->assertStatus(400);
    }

    public function test_set_payment_method_rejects_for_other_users_transaction(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $entry = Status::factory()->create(['code' => 'entry']);
        $paymentMethod = PaymentMethod::factory()->create(['is_active' => true]);
        $transaction = $this->makeTransactionFor($owner, $entry->id);

        $response = $this->withHeaders($this->headers($stranger))->postJson('/api/v1/payment/set-payment-method', [
            'transaction_id' => $transaction->id,
            'payment_method' => $paymentMethod->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_pay_with_gopay_succeeds(): void
    {
        Http::fake(['*' => Http::response([
            'status_code' => '201',
            'transaction_status' => 'pending',
            'actions' => [
                ['name' => 'deeplink-redirect', 'url' => 'https://gopay.example/deeplink'],
            ],
        ], 200)]);

        $user = User::factory()->create();
        $transaction = $this->makeTransactionFor($user);

        $response = $this->withHeaders($this->headers($user))->postJson("/api/v1/payment/{$transaction->id}", [
            'payment_method' => PaymentMethod::ID_GOPAY,
        ]);

        $response->assertOk();
        $this->assertSame('https://gopay.example/deeplink', $response->json('data.deep_link_url'));
        $this->assertEquals($transaction->fresh()->paid_amount, $transaction->fresh()->total_amount);
    }

    public function test_pay_with_gopay_fails_when_midtrans_rejects(): void
    {
        Http::fake(['*' => Http::response(['status_code' => '400', 'status_message' => 'Rejected'], 400)]);

        $user = User::factory()->create();
        $transaction = $this->makeTransactionFor($user);

        $response = $this->withHeaders($this->headers($user))->postJson("/api/v1/payment/{$transaction->id}", [
            'payment_method' => PaymentMethod::ID_GOPAY,
        ]);

        $response->assertStatus(500);
    }

    public function test_pay_rejects_for_other_users_transaction(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $transaction = $this->makeTransactionFor($owner);

        $response = $this->withHeaders($this->headers($stranger))->postJson("/api/v1/payment/{$transaction->id}", [
            'payment_method' => PaymentMethod::ID_GOPAY,
        ]);

        $response->assertStatus(403);
    }

    public function test_midtrans_callback_updates_transaction_to_settlement_when_found(): void
    {
        // same reasoning as above: skip the TransactionPaid side effects
        // (e-ticket PDF generation, email, Telegram) unrelated to this test.
        Event::fake([TransactionPaid::class]);
        Http::fake(['*' => Http::response(['transaction_status' => 'settlement'], 200)]);

        $pending = Status::factory()->create(['code' => 'pending']);
        Status::factory()->create(['code' => 'settlement']);
        $user = User::factory()->create();
        $transaction = $this->makeTransactionFor($user, $pending->id);

        $response = $this->postJson('/api/v1/payment/midtrans/callback', [
            'order_id' => $transaction->id,
        ]);

        $response->assertOk();
        $this->assertSame('settlement', $transaction->fresh()->status->code);
    }

    public function test_midtrans_callback_returns_not_found_for_unknown_transaction(): void
    {
        Http::fake(['*' => Http::response(['transaction_status' => 'settlement'], 200)]);

        $response = $this->postJson('/api/v1/payment/midtrans/callback', [
            'order_id' => (string) Str::uuid(),
        ]);

        $response->assertStatus(404);
    }
}
