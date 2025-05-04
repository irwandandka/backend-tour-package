<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies =  [
            [
                'name' => 'Indonesian Rupiah',
                'code' => 'IDR',
                'symbol' => 'Rp',
                'is_active' => true,
            ],
            [
                'name' => 'United States Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'is_active' => true,
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'is_active' => true,
            ],
            [
                'name' => 'Japanese Yen',
                'code' => 'JPY',
                'symbol' => '¥',
                'is_active' => true,
            ],
            [
                'name' => 'South Korean Won',
                'code' => 'KRW',
                'symbol' => '₩',
                'is_active' => true,
            ],
            [
                'name' => 'Chinese Yuan',
                'code' => 'CNY',
                'symbol' => '¥',
                'is_active' => true,
            ],
            [
                'name' => 'Singapore Dollar',
                'code' => 'SGD',
                'symbol' => 'S$',
                'is_active' => true,
            ],
            [
                'name' => 'Thai Baht',
                'code' => 'THB',
                'symbol' => '฿',
                'is_active' => true,
            ],
            [
                'name' => 'Vietnamese Dong',
                'code' => 'VND',
                'symbol' => '₫',
                'is_active' => true,
            ],
            [
                'name' => 'Malaysian Ringgit',
                'code' => 'MYR',
                'symbol' => 'RM',
                'is_active' => true,
            ],
            [
                'name' => 'British Pound Sterling',
                'code' => 'GBP',
                'symbol' => '£',
                'is_active' => true,
            ],
            [
                'name' => 'Australian Dollar',
                'code' => 'AUD',
                'symbol' => 'A$',
                'is_active' => true,
            ],
            [
                'name' => 'Canadian Dollar',
                'code' => 'CAD',
                'symbol' => 'C$',
                'is_active' => true,
            ],
            [
                'name' => 'Swiss Franc',
                'code' => 'CHF',
                'symbol' => 'CHF',
                'is_active' => true,
            ],
            [
                'name' => 'New Zealand Dollar',
                'code' => 'NZD',
                'symbol' => 'NZ$',
                'is_active' => true,
            ],
            [
                'name' => 'Hong Kong Dollar',
                'code' => 'HKD',
                'symbol' => 'HK$',
                'is_active' => true,
            ],
            [
                'name' => 'Mexican Peso',
                'code' => 'MXN',
                'symbol' => '$',
                'is_active' => true,
            ],
            [
                'name' => 'Brazilian Real',
                'code' => 'BRL',
                'symbol' => 'R$',
                'is_active' => true,
            ],
            [
                'name' => 'South African Rand',
                'code' => 'ZAR',
                'symbol' => 'R',
                'is_active' => true,
            ],
            [
                'name' => 'Russian Ruble',
                'code' => 'RUB',
                'symbol' => '₽',
                'is_active' => true,
            ],
            [
                'name' => 'Indian Rupee',
                'code' => 'INR',
                'symbol' => '₹',
                'is_active' => true,
            ],
            [
                'name' => 'Turkish Lira',
                'code' => 'TRY',
                'symbol' => '₺',
                'is_active' => true,
            ],
            [
                'name' => 'Saudi Riyal',
                'code' => 'SAR',
                'symbol' => 'ر.س',
                'is_active' => true,
            ],
            [
                'name' => 'United Arab Emirates Dirham',
                'code' => 'AED',
                'symbol' => 'د.إ',
                'is_active' => true,
            ],
            [
                'name' => 'Philippine Peso',
                'code' => 'PHP',
                'symbol' => '₱',
                'is_active' => true,
            ],
            [
                'name' => 'Bangladeshi Taka',
                'code' => 'BDT',
                'symbol' => '৳',
                'is_active' => true,
            ],
            [
                'name' => 'Pakistani Rupee',
                'code' => 'PKR',
                'symbol' => '₨',
                'is_active' => true,
            ],
            [
                'name' => 'Egyptian Pound',
                'code' => 'EGP',
                'symbol' => 'ج.م',
                'is_active' => true,
            ],
        ];

        DB::transaction(function () use ($currencies) {
            foreach ($currencies as $currency) {
                Currency::create($currency);
            }
        });
    }
}
