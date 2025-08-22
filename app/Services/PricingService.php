<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductDetail;

class PricingService
{
    private $currencyService;
    public function __construct()
    {
        $this->currencyService = new CurrencyService;
    }

    public function getPricing(ProductDetail $productDetail, $params, Currency $targetCurrency)
    {
        $purchaseCurrency = $productDetail->product->purchase_currency;
        $salesCurrency = $productDetail->product->sales_currency;

        $purchasePrice = $salesPrice = 0;

        $productPrice = $productDetail
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

    public function getListPricing(ProductDetail $productDetail, $params, Currency $targetCurrency)
    {
        $purchaseCurrency = $productDetail->product->purchase_currency;
        $salesCurrency = $productDetail->product->sales_currency;

        $purchasePrice = $salesPrice = 0;

        $productPrices = $productDetail
            ->product_prices
            ->sortBy('level')
            ->values();

        if (!$productPrices) return [];

        $productPrices = $productPrices
            ->map(function ($productPrice) use (
                $purchaseCurrency,
                $salesCurrency,
                $targetCurrency,
                $params
            ) {
                $types = ['adult', 'child', 'infant', 'senior'];

                $pricePerTypes = [];

                foreach ($types as $type) {
                    $purchasePrice = $salesPrice = 0;

                    if ($purchaseCurrency->code === $params['currency']) {
                        $purchasePrice = $productPrice->{"purchase_$type"};
                    } else {
                        $purchasePrice = $this->currencyService->convert(
                            $productPrice->{"purchase_$type"},
                            $purchaseCurrency,
                            $targetCurrency
                        );
                    }

                    if ($salesCurrency->code === $params['currency']) {
                        $salesPrice = $productPrice->{"sales_$type"};
                    } else {
                        $salesPrice = $this->currencyService->convert(
                            $productPrice->{"sales_$type"},
                            $salesCurrency,
                            $targetCurrency
                        );
                    }

                    $pricePerTypes[$type] = $purchasePrice + $salesPrice;
                }

                $pricePerTypes['level'] = $productPrice->level;

                return $pricePerTypes;
            });

        return $productPrices;
    }

    public function calculatePricing(Product $product, $currencies, $detail, $params)
    {
        $targetCurrency = $currencies->where('code', $params['currency'])->first();
        $currencySGD = $currencies->where('code', 'SGD')->first();

        $pricingDetail = [];

        $typePrices = ['adult', 'child', 'infant', 'senior'];

        $productDetail = $product
            ->product_details
            ->where('id', $detail['product_detail'])
            ->first();

        $purchaseTotal = $purchaseTotalBase = $purchaseSubtotal = $purchaseSubtotalBase = $salesSubtotal = $salesSubtotalBase = $salesTotal = $salesTotalBase = 0;

        $pricingDetail = [];
        $level = 0;

        foreach ($typePrices as $type) {
            if ($detail["quantity_$type"] == 0) {
                continue;
            }

            $purchasePrice = $purchaseInSGD = $salesPrice = $salesInSGD = 0;

            $countPaxPerType = 1;

            while ($countPaxPerType <= $detail["quantity_$type"]) {
                $level++;

                $productPrice = $productDetail
                    ->product_prices
                    ->where('level', $level)
                    ->first();

                if (!$productPrice) continue;

                // Target Currency
                $purchasePrice += $this->currencyService->convert(
                    $productPrice->{"purchase_$type"},
                    $product->purchase_currency,
                    $targetCurrency
                );

                // Base Currency
                $purchaseInSGD += $this->currencyService->convert(
                    $productPrice->{"purchase_$type"},
                    $product->purchase_currency,
                    $currencySGD
                );

                // Target Currency
                $salesPrice += $this->currencyService->convert(
                    $productPrice->{"sales_$type"},
                    $product->sales_currency,
                    $targetCurrency
                );

                // Base Currency
                $salesInSGD += $this->currencyService->convert(
                    $productPrice->{"sales_$type"},
                    $product->sales_currency,
                    $currencySGD
                );

                $countPaxPerType++;
            }

            $pricingDetail["quantity_$type"] = $detail["quantity_$type"];

            $pricingDetail["purchase_$type"] = $purchasePrice;
            $purchaseSubtotalBase += $purchaseInSGD;
            $purchaseSubtotal += $purchasePrice;

            $pricingDetail["sales_$type"] = ($purchasePrice + $salesPrice);
            $salesSubtotal += ($purchasePrice + $salesPrice);
            $salesSubtotalBase += ($purchaseInSGD + $salesInSGD);
        }

        $purchaseTotal += $purchaseSubtotal;
        $purchaseTotalBase += $purchaseSubtotalBase;

        $pricingDetail['purchase_total'] = $purchaseTotal;
        $pricingDetail['purchase_total_base'] = $purchaseTotalBase;
        $pricingDetail['purchase_subtotal'] = $purchaseSubtotal;
        $pricingDetail['purchase_subtotal_base'] = $purchaseSubtotalBase;

        $salesTotal += $salesSubtotal;
        $salesTotalBase += $salesSubtotalBase;

        $pricingDetail['sales_total'] = $salesTotal;
        $pricingDetail['sales_total_base'] = $salesTotalBase;
        $pricingDetail['sales_subtotal'] = $salesSubtotal;
        $pricingDetail['sales_subtotal_base'] = $salesSubtotalBase;

        $pricingDetail['product_detail_id'] = $productDetail->id;
        $pricingDetail['product_id'] = $product->id;
        $pricingDetail['quantity'] = 1;
        $pricingDetail['date_from'] = $params['date_from'];
        $pricingDetail['date_to'] = $params['date_to'];

        return $pricingDetail;
    }
}
