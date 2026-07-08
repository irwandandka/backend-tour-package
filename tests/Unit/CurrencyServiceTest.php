<?php

namespace Tests\Unit;

use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_convert_returns_same_amount_when_currency_is_identical(): void
    {
        $service = new CurrencyService();
        $usd = Currency::factory()->create(['code' => 'USD']);

        $result = $service->convert(100, $usd, $usd);

        $this->assertSame(100, $result);
    }

    public function test_convert_uses_direct_exchange_rate_when_available(): void
    {
        $service = new CurrencyService();
        $usd = Currency::factory()->create(['code' => 'USD']);
        $idr = Currency::factory()->create(['code' => 'IDR']);

        ExchangeRate::factory()->create([
            'base_currency_id' => $usd->id,
            'target_currency_id' => $idr->id,
            'exchange_rate' => 15000,
        ]);

        $result = $service->convert(10, $usd, $idr);

        $this->assertEqualsWithDelta(150000, (float) $result, 0.01);
    }

    public function test_convert_falls_back_through_sgd_when_no_direct_rate(): void
    {
        $service = new CurrencyService();
        $usd = Currency::factory()->create(['code' => 'USD']);
        $sgd = Currency::factory()->create(['code' => 'SGD']);
        $idr = Currency::factory()->create(['code' => 'IDR']);

        // no direct USD -> IDR rate, only via SGD
        ExchangeRate::factory()->create([
            'base_currency_id' => $usd->id,
            'target_currency_id' => $sgd->id,
            'exchange_rate' => 1.35,
        ]);
        ExchangeRate::factory()->create([
            'base_currency_id' => $sgd->id,
            'target_currency_id' => $idr->id,
            'exchange_rate' => 11000,
        ]);

        $result = $service->convert(10, $usd, $idr);

        $this->assertEqualsWithDelta(10 * 1.35 * 11000, (float) $result, 0.01);
    }

    public function test_convert_throws_when_no_rate_available_at_all(): void
    {
        $service = new CurrencyService();
        $usd = Currency::factory()->create(['code' => 'USD']);
        Currency::factory()->create(['code' => 'SGD']);
        $idr = Currency::factory()->create(['code' => 'IDR']);
        // no exchange rates created at all, direct or via SGD

        $this->expectException(\Exception::class);

        $service->convert(10, $usd, $idr);
    }
}
