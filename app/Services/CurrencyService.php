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
        // Jika mata uang asal dan tujuan sama, tidak perlu konversi
        if ($fromCurrency->id === $toCurrency->id) {
            return $amount;
        }

        // Coba ambil kurs langsung
        $directRate = $this->getCurrencyRate($fromCurrency, $toCurrency);
        if ($directRate) {
            return $amount * $directRate;
        }


        // Jika kurs langsung tidak ada, cek apakah bisa melewati SGD
        $rateToSGD = $this->getCurrencyRate($fromCurrency, 'SGD');
        $rateFromSGD = $this->getCurrencyRate('SGD', $toCurrency);

        if ($rateToSGD && $rateFromSGD) {
            // Konversi melewati SGD
            return $amount * $rateToSGD * $rateFromSGD;
        }

        // Jika tidak ada kurs langsung dan tidak bisa melewati SGD, lempar error
        throw new Exception("Exchange rate not available for conversion.");
    }
}
