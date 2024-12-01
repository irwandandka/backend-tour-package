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
                'name' => 'US Dollar',
                'code' => 'USD',
                'exchange_rate' => 1.000000,
                'symbol' => '$',
                'is_active' => true
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'exchange_rate' => 1.100000,
                'symbol' => '€',
                'is_active' => true
            ],
            [
                'name' => 'Indonesian Rupiah',
                'code' => 'IDR',
                'exchange_rate' => 0.000065,
                'symbol' => 'Rp',
                'is_active' => true
            ],
            [
                'name' => 'Japanese Yen',
                'code' => 'JPY',
                'exchange_rate' => 0.007500,
                'symbol' => '¥',
                'is_active' => true
            ],
            [
                'name' => 'Australian Dollar',
                'code' => 'AUD',
                'exchange_rate' => 0.750000,
                'symbol' => 'A$',
                'is_active' => true
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
