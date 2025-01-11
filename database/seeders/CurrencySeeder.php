<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'USD',
                'symbol' => '$',
                'name' => 'United States Dollar',
                'exchange_rate' => 1.00, // Base rate
                'is_active' => true,
            ],
            [
                'code' => 'EUR',
                'symbol' => '€',
                'name' => 'Euro',
                'exchange_rate' => 0.85, // Example rate
                'is_active' => true,
            ],
            [
                'code' => 'GBP',
                'symbol' => '£',
                'name' => 'British Pound',
                'exchange_rate' => 0.75,
                'is_active' => true,
            ],
            [
                'code' => 'JPY',
                'symbol' => '¥',
                'name' => 'Japanese Yen',
                'exchange_rate' => 110.00,
                'is_active' => true,
            ],
            [
                'code' => 'IDR',
                'symbol' => 'Rp',
                'name' => 'Indonesian Rupiah',
                'exchange_rate' => 14000.00,
                'is_active' => true,
            ],
            [
                'code' => 'AUD',
                'symbol' => 'A$',
                'name' => 'Australian Dollar',
                'exchange_rate' => 1.30,
                'is_active' => true,
            ],
            [
                'code' => 'CAD',
                'symbol' => 'C$',
                'name' => 'Canadian Dollar',
                'exchange_rate' => 1.25,
                'is_active' => true,
            ],
            [
                'code' => 'CHF',
                'symbol' => 'CHF',
                'name' => 'Swiss Franc',
                'exchange_rate' => 0.92,
                'is_active' => true,
            ],
            [
                'code' => 'CNY',
                'symbol' => '¥',
                'name' => 'Chinese Yuan',
                'exchange_rate' => 6.45,
                'is_active' => true,
            ],
            [
                'code' => 'INR',
                'symbol' => '₹',
                'name' => 'Indian Rupee',
                'exchange_rate' => 74.00,
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
