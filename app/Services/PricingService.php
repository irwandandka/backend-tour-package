<?php

namespace App\Services;

class PricingService
{
    private $currencyService;
    public function __construct()
    {
        $this->currencyService = new CurrencyService;
    }

    public function getPricing($product, $params, $targetCurrency)
    {
        $purchaseCurrency = $product->purchase_currency;
        $salesCurrency = $product->sales_currency;

        $purchasePrice = $salesPrice = 0;

        $productPrice = $product
            ->product_prices
            ->where('level', 1)
            ->first();

        if (!$productPrice) return 0;

        if ($purchaseCurrency->code === $params['currency']) {
            $purchasePrice = $productPrice->purchase_adult;
        } else {
            $purchasePrice = $this->currencyService->convert(
                $productPrice->purchase_adult,
                $purchaseCurrency,
                $targetCurrency
            );
        }

        if ($salesCurrency->code === $params['currency']) {
            $salesPrice = $productPrice->sales_adult;
        } else {
            $salesPrice = $this->currencyService->convert(
                $productPrice->sales_adult,
                $salesCurrency,
                $targetCurrency
            );
        }

        return $purchasePrice + $salesPrice;
    }
}
