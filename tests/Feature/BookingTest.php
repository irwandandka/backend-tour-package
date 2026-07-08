<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductPrice;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private Currency $sgdCurrency;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.internal.api_key' => 'test-api-key']);
        Status::factory()->create(['code' => 'entry']);
        // cancelBooking() sets status_id to this hardcoded constant directly,
        // so a real row with that exact primary key must exist (FK constraint).
        Status::factory()->create(['id' => Status::STATUS_CANCELLED, 'code' => 'cancel']);
        // PricingService::calculatePricing always converts a base amount into
        // SGD regardless of the requested currency, so SGD must always exist.
        $this->sgdCurrency = Currency::factory()->create(['code' => 'SGD']);
    }

    private function headers(User $user, array $extra = []): array
    {
        $token = $user->createToken('test')->plainTextToken;

        return array_merge([
            'X-API-KEY' => 'test-api-key',
            'Authorization' => 'Bearer '.$token,
        ], $extra);
    }

    private function makeBookableProduct(): array
    {
        $currency = Currency::factory()->create(['code' => 'USD']);
        ExchangeRate::factory()->create([
            'base_currency_id' => $currency->id,
            'target_currency_id' => $this->sgdCurrency->id,
            'exchange_rate' => 1,
        ]);
        $product = Product::factory()->create([
            'purchase_currency_id' => $currency->id,
            'sales_currency_id' => $currency->id,
        ]);
        $detail = ProductDetail::factory()->create(['product_id' => $product->id]);
        ProductPrice::factory()->create(['product_detail_id' => $detail->id, 'level' => 1]);

        return [$product, $detail, $currency];
    }

    public function test_create_booking_succeeds_with_valid_payload(): void
    {
        $user = User::factory()->create();
        [$product, $detail, $currency] = $this->makeBookableProduct();

        $response = $this->withHeaders($this->headers($user))->postJson('/api/v1/booking', [
            'currency' => $currency->code,
            'product_id' => $product->id,
            'date_from' => '2026-08-01',
            'date_to' => '2026-08-05',
            'product_details' => [
                [
                    'product_detail' => $detail->id,
                    'quantity' => 1,
                    'quantity_adult' => 1,
                    'quantity_child' => 0,
                    'quantity_infant' => 0,
                    'quantity_senior' => 0,
                ],
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
        $this->assertDatabaseCount('transaction_details', 1);
    }

    public function test_create_booking_fails_when_product_does_not_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->headers($user))->postJson('/api/v1/booking', [
            'currency' => 'USD',
            'product_id' => (string) \Illuminate\Support\Str::uuid(),
            'date_from' => '2026-08-01',
            'date_to' => '2026-08-05',
            'product_details' => [
                [
                    'product_detail' => (string) \Illuminate\Support\Str::uuid(),
                    'quantity' => 1,
                    'quantity_adult' => 1,
                    'quantity_child' => 0,
                    'quantity_infant' => 0,
                    'quantity_senior' => 0,
                ],
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_create_booking_with_all_zero_quantities_results_in_zero_total(): void
    {
        $user = User::factory()->create();
        [$product, $detail, $currency] = $this->makeBookableProduct();

        $response = $this->withHeaders($this->headers($user))->postJson('/api/v1/booking', [
            'currency' => $currency->code,
            'product_id' => $product->id,
            'date_from' => '2026-08-01',
            'date_to' => '2026-08-05',
            'product_details' => [
                [
                    'product_detail' => $detail->id,
                    'quantity' => 1,
                    'quantity_adult' => 0,
                    'quantity_child' => 0,
                    'quantity_infant' => 0,
                    'quantity_senior' => 0,
                ],
            ],
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.total_amount', 0);
    }

    private function createOwnedTransaction(User $owner): Transaction
    {
        [$product, $detail, $currency] = $this->makeBookableProduct();

        return Transaction::factory()->create([
            'user_id' => $owner->id,
            'product_id' => $product->id,
            'currency_id' => $currency->id,
        ]);
    }

    public function test_owner_can_view_their_own_booking(): void
    {
        $owner = User::factory()->create();
        $transaction = $this->createOwnedTransaction($owner);

        $this->withHeaders($this->headers($owner))
            ->getJson("/api/v1/booking/{$transaction->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $transaction->id);
    }

    public function test_other_user_cannot_view_someone_elses_booking(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $transaction = $this->createOwnedTransaction($owner);

        $this->withHeaders($this->headers($stranger))
            ->getJson("/api/v1/booking/{$transaction->id}")
            ->assertStatus(403);
    }

    public function test_owner_can_cancel_their_own_booking(): void
    {
        $owner = User::factory()->create();
        $transaction = $this->createOwnedTransaction($owner);

        $this->withHeaders($this->headers($owner))
            ->postJson("/api/v1/booking/{$transaction->id}/cancel")
            ->assertOk();

        $this->assertSame(Status::STATUS_CANCELLED, $transaction->fresh()->status_id);
    }

    public function test_other_user_cannot_cancel_someone_elses_booking(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $transaction = $this->createOwnedTransaction($owner);

        $this->withHeaders($this->headers($stranger))
            ->postJson("/api/v1/booking/{$transaction->id}/cancel")
            ->assertStatus(403);
    }

    public function test_other_user_cannot_update_someone_elses_booking(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $transaction = $this->createOwnedTransaction($owner);

        $this->withHeaders($this->headers($stranger))
            ->postJson("/api/v1/booking/{$transaction->id}/update", [
                'name' => 'Stranger',
                'email' => 'stranger@example.com',
                'phone' => '0800000000',
                'address' => 'Somewhere',
                'postal_code' => '12345',
                'passengers' => [
                    ['first_name' => 'A', 'last_name' => 'B', 'title' => 'Mr'],
                ],
            ])
            ->assertStatus(403);
    }

    public function test_update_rejects_fewer_passengers_than_total_pax(): void
    {
        $owner = User::factory()->create();
        [$product, $detail, $currency] = $this->makeBookableProduct();
        $transaction = Transaction::factory()->create([
            'user_id' => $owner->id,
            'product_id' => $product->id,
            'currency_id' => $currency->id,
        ]);
        \App\Models\TransactionDetail::factory()->create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_detail_id' => $detail->id,
            'quantity_adult' => 2,
            'quantity_child' => 0,
            'quantity_infant' => 0,
            'quantity_senior' => 0,
        ]);

        $response = $this->withHeaders($this->headers($owner))
            ->postJson("/api/v1/booking/{$transaction->id}/update", [
                'name' => 'Owner',
                'email' => 'owner@example.com',
                'phone' => '0800000000',
                'address' => 'Somewhere',
                'postal_code' => '12345',
                'passengers' => [
                    ['first_name' => 'Only', 'last_name' => 'One', 'title' => 'Mr'],
                ],
            ]);

        $response->assertStatus(422);
    }

    public function test_other_user_cannot_review_someone_elses_booking(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $transaction = $this->createOwnedTransaction($owner);

        $this->withHeaders($this->headers($stranger))
            ->postJson("/api/v1/booking/{$transaction->id}/submit-review", [
                'rating' => 5,
                'comment' => 'Great trip',
            ])
            ->assertStatus(403);
    }

    public function test_owner_can_review_their_own_booking(): void
    {
        $owner = User::factory()->create();
        $transaction = $this->createOwnedTransaction($owner);

        $this->withHeaders($this->headers($owner))
            ->postJson("/api/v1/booking/{$transaction->id}/submit-review", [
                'rating' => 5,
                'comment' => 'Great trip',
            ])
            ->assertOk();

        $this->assertDatabaseHas('reviews', [
            'user_id' => $owner->id,
            'product_id' => $transaction->product_id,
            'rating' => 5,
        ]);
    }
}
