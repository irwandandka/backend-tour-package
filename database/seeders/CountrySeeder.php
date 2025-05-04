<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'South Korea',
                'iso_code' => 'KR',
                'phone_code' => '+82',
                'region_id' => '55e2b614-3182-40c3-8ae3-b72582822747',
            ],
            [
                'name' => 'Japan',
                'iso_code' => 'JP',
                'phone_code' => '+81',
                'region_id' => '55e2b614-3182-40c3-8ae3-b72582822747',
            ],
            [
                'name' => 'China',
                'iso_code' => 'CN',
                'phone_code' => '+86',
                'region_id' => '55e2b614-3182-40c3-8ae3-b72582822747',
            ],
            [
                'name' => 'Indonesia',
                'iso_code' => 'ID',
                'phone_code' => '+62',
                'region_id' => '968659f9-98d3-4df9-973a-6c84b92a88a6',
            ],
            [
                'name' => 'Thailand',
                'iso_code' => 'TH',
                'phone_code' => '+66',
                'region_id' => '968659f9-98d3-4df9-973a-6c84b92a88a6',
            ],
            [
                'name' => 'Vietnam',
                'iso_code' => 'VN',
                'phone_code' => '+84',
                'region_id' => '968659f9-98d3-4df9-973a-6c84b92a88a6',
            ],
            [
                'name' => 'Malaysia',
                'iso_code' => 'MY',
                'phone_code' => '+60',
                'region_id' => '968659f9-98d3-4df9-973a-6c84b92a88a6',
            ],
            [
                'name' => 'Singapore',
                'iso_code' => 'SG',
                'phone_code' => '+65',
                'region_id' => '968659f9-98d3-4df9-973a-6c84b92a88a6',
            ],
            [
                'name' => 'Italy',
                'iso_code' => 'IT',
                'phone_code' => '+39',
                'region_id' => '31fbf1d6-2017-44ff-a2b9-3d2480499d42',
            ],
            [
                'name' => 'United Kingdom',
                'iso_code' => 'GB',
                'phone_code' => '+44',
                'region_id' => '31fbf1d6-2017-44ff-a2b9-3d2480499d42',
            ],
            [
                'name' => 'Portugal',
                'iso_code' => 'PT',
                'phone_code' => '+351',
                'region_id' => '31fbf1d6-2017-44ff-a2b9-3d2480499d42',
            ],
            [
                'name' => 'Saudi Arabia',
                'iso_code' => 'SA',
                'phone_code' => '+966',
                'region_id' => 'e07688c0-9abf-4185-b682-85314cde48fd',
            ],
            [
                'name' => 'Egypt',
                'iso_code' => 'EG',
                'phone_code' => '+20',
                'region_id' => 'e07688c0-9abf-4185-b682-85314cde48fd',
            ],
            [
                'name' => 'Kuwait',
                'iso_code' => 'KW',
                'phone_code' => '+965',
                'region_id' => 'e07688c0-9abf-4185-b682-85314cde48fd',
            ]
        ];
        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
