<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'Indonesian Rupiah',
                'code' => 'IDR',
                'symbol' => 'Rp',
                'is_active' => true,
                // exchange rate from sgd to idr
                'exchange_rate' => 11000, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'United States Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'is_active' => true,
                // exchange rate from sgd to usd
                'exchange_rate' => 1.35, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'is_active' => true,
                // exchange rate from sgd to eur
                'exchange_rate' => 1.15, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Japanese Yen',
                'code' => 'JPY',
                'symbol' => '¥',
                'is_active' => true,
                // exchange rate from sgd to jpy
                'exchange_rate' => 150, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'South Korean Won',
                'code' => 'KRW',
                'symbol' => '₩',
                'is_active' => true,
                // exchange rate from sgd to krw
                'exchange_rate' => 1200, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Chinese Yuan',
                'code' => 'CNY',
                'symbol' => '¥',
                'is_active' => true,
                // exchange rate from sgd to cny
                'exchange_rate' => 6.5, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Singapore Dollar',
                'code' => 'SGD',
                'symbol' => 'S$',
                'is_active' => true,
                // exchange rate from sgd to sgd
                'exchange_rate' => 1.0, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Thai Baht',
                'code' => 'THB',
                'symbol' => '฿',
                'is_active' => true,
                // exchange rate from sgd to thb
                'exchange_rate' => 35, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Vietnamese Dong',
                'code' => 'VND',
                'symbol' => '₫',
                'is_active' => true,
                // exchange rate from sgd to vnd
                'exchange_rate' => 23000, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Malaysian Ringgit',
                'code' => 'MYR',
                'symbol' => 'RM',
                'is_active' => true,
                // exchange rate from sgd to myr
                'exchange_rate' => 3.2, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'British Pound Sterling',
                'code' => 'GBP',
                'symbol' => '£',
                'is_active' => true,
                // exchange rate from sgd to gbp
                'exchange_rate' => 0.85, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Australian Dollar',
                'code' => 'AUD',
                'symbol' => 'A$',
                'is_active' => true,
                // exchange rate from sgd to aud
                'exchange_rate' => 1.4, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Canadian Dollar',
                'code' => 'CAD',
                'symbol' => 'C$',
                'is_active' => true,
                // exchange rate from sgd to cad
                'exchange_rate' => 1.25, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Swiss Franc',
                'code' => 'CHF',
                'symbol' => 'CHF',
                'is_active' => true,
                // exchange rate from sgd to chf
                'exchange_rate' => 1.2, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'New Zealand Dollar',
                'code' => 'NZD',
                'symbol' => 'NZ$',
                'is_active' => true,
                // exchange rate from sgd to nzd
                'exchange_rate' => 1.5, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Hong Kong Dollar',
                'code' => 'HKD',
                'symbol' => 'HK$',
                'is_active' => true,
                // exchange rate from sgd to hkd
                'exchange_rate' => 7.8, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Mexican Peso',
                'code' => 'MXN',
                'symbol' => '$',
                'is_active' => true,
                // exchange rate from sgd to mxn
                'exchange_rate' => 27, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Brazilian Real',
                'code' => 'BRL',
                'symbol' => 'R$',
                'is_active' => true,
                // exchange rate from sgd to brl
                'exchange_rate' => 5.5, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'South African Rand',
                'code' => 'ZAR',
                'symbol' => 'R',
                'is_active' => true,
                // exchange rate from sgd to zar
                'exchange_rate' => 15, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Russian Ruble',
                'code' => 'RUB',
                'symbol' => '₽',
                'is_active' => true,
                // exchange rate from sgd to rub
                'exchange_rate' => 75, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Indian Rupee',
                'code' => 'INR',
                'symbol' => '₹',
                'is_active' => true,
                // exchange rate from sgd to inr
                'exchange_rate' => 75, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Turkish Lira',
                'code' => 'TRY',
                'symbol' => '₺',
                'is_active' => true,
                // exchange rate from sgd to try
                'exchange_rate' => 20, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Saudi Riyal',
                'code' => 'SAR',
                'symbol' => 'ر.س',
                'is_active' => true,
                // exchange rate from sgd to sar
                'exchange_rate' => 3.75, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'United Arab Emirates Dirham',
                'code' => 'AED',
                'symbol' => 'د.إ',
                'is_active' => true,
                // exchange rate from sgd to aed
                'exchange_rate' => 3.67, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Philippine Peso',
                'code' => 'PHP',
                'symbol' => '₱',
                'is_active' => true,
                // exchange rate from sgd to php
                'exchange_rate' => 55, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Bangladeshi Taka',
                'code' => 'BDT',
                'symbol' => '৳',
                'is_active' => true,
                // exchange rate from sgd to bdt
                'exchange_rate' => 85, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Pakistani Rupee',
                'code' => 'PKR',
                'symbol' => '₨',
                'is_active' => true,
                // exchange rate from sgd to pkr
                'exchange_rate' => 200, // Example exchange rate, adjust as needed
            ],
            [
                'name' => 'Egyptian Pound',
                'code' => 'EGP',
                'symbol' => 'ج.م',
                'is_active' => true,
                // exchange rate from sgd to egp
                'exchange_rate' => 15.7, // Example exchange rate, adjust as needed
            ],
        ];

        DB::transaction(function () use ($currencies) {
            foreach ($currencies as $currency) {
                Currency::create($currency);
            }
        });
    }
}
