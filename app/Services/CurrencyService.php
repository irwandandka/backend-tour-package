<?php

namespace App\Services;

use Exception;

class CurrencyService
{
    public function getCurrencyRate($fromCurrency, $toCurrency)
    {
        $rate = 0;

        $exchangeRate = $fromCurrency
            ->baseExchangeRates
            ->where('target_currency_id', $toCurrency->id)
            ->first();

        if ($exchangeRate) {
            $rate = $exchangeRate->exchange_rate;
        } else {
            throw new Exception('Exchange rate not found');
        }

        return $rate;
    }

    public function convert($amount, $fromCurrency, $toCurrency)
    {
        $rate = $this->getCurrencyRate($fromCurrency, $toCurrency);

        return $amount * $rate;
    }
}
