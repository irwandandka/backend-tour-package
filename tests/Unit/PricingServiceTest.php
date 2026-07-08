<?php

namespace Tests\Unit;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductPrice;
use App\Services\CurrencyService;
use App\Services\PricingService;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    private function makeCurrency(string $id, string $code): Currency
    {
        $currency = new Currency(['code' => $code, 'name' => $code, 'symbol' => $code, 'is_active' => true]);
        $currency->id = $id;

        return $currency;
    }

    private function makeProductPrice(int $level, array $prices): ProductPrice
    {
        $price = new ProductPrice($prices);
        $price->id = 'price-'.$level;
        $price->level = $level;

        return $price;
    }

    private function makeProductDetail(Product $product, $productPrices): ProductDetail
    {
        $detail = new ProductDetail(['name_en' => 'Detail', 'activity_image' => 'x.jpg']);
        $detail->id = 'detail-1';
        $detail->setRelation('product', $product);
        $detail->setRelation('product_prices', collect($productPrices));

        return $detail;
    }

    private function makeProduct(Currency $purchaseCurrency, Currency $salesCurrency): Product
    {
        $product = new Product(['name' => 'Trip']);
        $product->id = 'product-1';
        $product->setRelation('purchase_currency', $purchaseCurrency);
        $product->setRelation('sales_currency', $salesCurrency);

        return $product;
    }

    public function test_calculate_pricing_sums_multiple_passenger_types(): void
    {
        $currencyService = $this->createMock(CurrencyService::class);
        $currencyService->method('convert')->willReturnArgument(0); // identity, no conversion

        $usd = $this->makeCurrency('currency-usd', 'USD');
        $sgd = $this->makeCurrency('currency-sgd', 'SGD');

        $level1 = $this->makeProductPrice(1, [
            'purchase_adult' => 100, 'sales_adult' => 150,
            'purchase_child' => 80, 'sales_child' => 120,
            'purchase_infant' => 0, 'sales_infant' => 0,
            'purchase_senior' => 0, 'sales_senior' => 0,
        ]);
        $level2 = $this->makeProductPrice(2, [
            'purchase_adult' => 110, 'sales_adult' => 160,
            'purchase_child' => 90, 'sales_child' => 130,
            'purchase_infant' => 0, 'sales_infant' => 0,
            'purchase_senior' => 0, 'sales_senior' => 0,
        ]);

        $product = $this->makeProduct($usd, $usd);
        $detail = $this->makeProductDetail($product, [$level1, $level2]);
        $product->setRelation('product_details', collect([$detail]));

        $pricing = new PricingService($currencyService);

        $result = $pricing->calculatePricing($product, collect([$usd, $sgd]), [
            'product_detail' => 'detail-1',
            'quantity_adult' => 1,
            'quantity_child' => 1,
            'quantity_infant' => 0,
            'quantity_senior' => 0,
        ], ['currency' => 'USD', 'date_from' => '2026-01-01', 'date_to' => '2026-01-05']);

        // adult consumes level 1 (100/150), child consumes level 2 (90/130)
        $this->assertSame(100.0, (float) $result['purchase_adult']);
        $this->assertSame(250.0, (float) $result['sales_adult']); // purchase+sales combined per code
        $this->assertSame(90.0, (float) $result['purchase_child']);
        $this->assertSame(220.0, (float) $result['sales_child']);
        $this->assertSame(190.0, (float) $result['purchase_total']);
        $this->assertSame(470.0, (float) $result['sales_total']);
        $this->assertSame(190.0, (float) $result['purchase_total_base']);
        $this->assertSame(470.0, (float) $result['sales_total_base']);
    }

    public function test_calculate_pricing_skips_passenger_types_with_zero_quantity(): void
    {
        $currencyService = $this->createMock(CurrencyService::class);
        $currencyService->method('convert')->willReturnArgument(0);

        $usd = $this->makeCurrency('currency-usd', 'USD');
        $level1 = $this->makeProductPrice(1, [
            'purchase_adult' => 100, 'sales_adult' => 150,
            'purchase_child' => 0, 'sales_child' => 0,
            'purchase_infant' => 0, 'sales_infant' => 0,
            'purchase_senior' => 0, 'sales_senior' => 0,
        ]);

        $product = $this->makeProduct($usd, $usd);
        $detail = $this->makeProductDetail($product, [$level1]);
        $product->setRelation('product_details', collect([$detail]));

        $pricing = new PricingService($currencyService);

        $result = $pricing->calculatePricing($product, collect([$usd]), [
            'product_detail' => 'detail-1',
            'quantity_adult' => 1,
            'quantity_child' => 0,
            'quantity_infant' => 0,
            'quantity_senior' => 0,
        ], ['currency' => 'USD', 'date_from' => '2026-01-01', 'date_to' => '2026-01-05']);

        $this->assertArrayHasKey('purchase_adult', $result);
        $this->assertArrayNotHasKey('purchase_child', $result);
        $this->assertArrayNotHasKey('purchase_infant', $result);
        $this->assertArrayNotHasKey('purchase_senior', $result);
    }

    public function test_calculate_pricing_stops_when_a_price_level_is_missing(): void
    {
        $currencyService = $this->createMock(CurrencyService::class);
        $currencyService->method('convert')->willReturnArgument(0);

        $usd = $this->makeCurrency('currency-usd', 'USD');
        $level1 = $this->makeProductPrice(1, [
            'purchase_adult' => 100, 'sales_adult' => 150,
            'purchase_child' => 0, 'sales_child' => 0,
            'purchase_infant' => 0, 'sales_infant' => 0,
            'purchase_senior' => 0, 'sales_senior' => 0,
        ]);
        $level2 = $this->makeProductPrice(2, [
            'purchase_adult' => 110, 'sales_adult' => 160,
            'purchase_child' => 0, 'sales_child' => 0,
            'purchase_infant' => 0, 'sales_infant' => 0,
            'purchase_senior' => 0, 'sales_senior' => 0,
        ]);
        // only 2 levels exist, but we book 3 adults -> the 3rd has no price row

        $product = $this->makeProduct($usd, $usd);
        $detail = $this->makeProductDetail($product, [$level1, $level2]);
        $product->setRelation('product_details', collect([$detail]));

        $pricing = new PricingService($currencyService);

        $result = $pricing->calculatePricing($product, collect([$usd]), [
            'product_detail' => 'detail-1',
            'quantity_adult' => 3,
            'quantity_child' => 0,
            'quantity_infant' => 0,
            'quantity_senior' => 0,
        ], ['currency' => 'USD', 'date_from' => '2026-01-01', 'date_to' => '2026-01-05']);

        // only level 1 + level 2 prices were accumulated (100 + 110), 3rd passenger silently unpriced
        $this->assertSame(210.0, (float) $result['purchase_adult']);
    }

    public function test_get_pricing_returns_zero_when_no_price_row_exists(): void
    {
        $currencyService = $this->createMock(CurrencyService::class);
        $usd = $this->makeCurrency('currency-usd', 'USD');
        $product = $this->makeProduct($usd, $usd);
        $detail = $this->makeProductDetail($product, []); // no product prices at all

        $pricing = new PricingService($currencyService);

        $result = $pricing->getPricing($detail, ['currency' => 'USD'], $usd);

        $this->assertSame(0, $result);
    }

    public function test_get_pricing_skips_conversion_when_currency_matches(): void
    {
        $currencyService = $this->createMock(CurrencyService::class);
        $currencyService->expects($this->never())->method('convert');

        $usd = $this->makeCurrency('currency-usd', 'USD');
        $level1 = $this->makeProductPrice(1, [
            'purchase_adult' => 100, 'sales_adult' => 150,
            'purchase_child' => 0, 'sales_child' => 0,
            'purchase_infant' => 0, 'sales_infant' => 0,
            'purchase_senior' => 0, 'sales_senior' => 0,
        ]);
        $product = $this->makeProduct($usd, $usd);
        $detail = $this->makeProductDetail($product, [$level1]);

        $pricing = new PricingService($currencyService);

        $result = $pricing->getPricing($detail, ['currency' => 'USD'], $usd);

        $this->assertSame(250.0, (float) $result); // purchase_adult + sales_adult, no conversion
    }

    public function test_get_pricing_converts_when_currency_differs(): void
    {
        $currencyService = $this->createMock(CurrencyService::class);
        $currencyService->expects($this->exactly(2))->method('convert')->willReturn(500.0);

        $usd = $this->makeCurrency('currency-usd', 'USD');
        $idr = $this->makeCurrency('currency-idr', 'IDR');
        $level1 = $this->makeProductPrice(1, [
            'purchase_adult' => 100, 'sales_adult' => 150,
            'purchase_child' => 0, 'sales_child' => 0,
            'purchase_infant' => 0, 'sales_infant' => 0,
            'purchase_senior' => 0, 'sales_senior' => 0,
        ]);
        $product = $this->makeProduct($usd, $usd);
        $detail = $this->makeProductDetail($product, [$level1]);

        $pricing = new PricingService($currencyService);

        $result = $pricing->getPricing($detail, ['currency' => 'IDR'], $idr);

        $this->assertSame(1000.0, (float) $result);
    }
}
