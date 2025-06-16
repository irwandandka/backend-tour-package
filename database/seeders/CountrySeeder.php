<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = Region::all();
        $countries = [
            [
                'name' => 'South Korea',
                'iso_code' => 'KR',
                'phone_code' => '+82',
                'region_id' => $regions->where('code', 'as')->first()->id,
            ],
            [
                'name' => 'Japan',
                'iso_code' => 'JP',
                'phone_code' => '+81',
                'region_id' => $regions->where('code', 'as')->first()->id,
            ],
            [
                'name' => 'China',
                'iso_code' => 'CN',
                'phone_code' => '+86',
                'region_id' => $regions->where('code', 'as')->first()->id,
            ],
            [
                'name' => 'Indonesia',
                'iso_code' => 'ID',
                'phone_code' => '+62',
                'region_id' => $regions->where('code', 'sea')->first()->id,
            ],
            [
                'name' => 'Thailand',
                'iso_code' => 'TH',
                'phone_code' => '+66',
                'region_id' => $regions->where('code', 'sea')->first()->id,
            ],
            [
                'name' => 'Vietnam',
                'iso_code' => 'VN',
                'phone_code' => '+84',
                'region_id' => $regions->where('code', 'sea')->first()->id,
            ],
            [
                'name' => 'Malaysia',
                'iso_code' => 'MY',
                'phone_code' => '+60',
                'region_id' => $regions->where('code', 'sea')->first()->id,
            ],
            [
                'name' => 'Singapore',
                'iso_code' => 'SG',
                'phone_code' => '+65',
                'region_id' => $regions->where('code', 'sea')->first()->id,
            ],
            [
                'name' => 'Italy',
                'iso_code' => 'IT',
                'phone_code' => '+39',
                'region_id' => $regions->where('code', 'eu')->first()->id,
            ],
            [
                'name' => 'United Kingdom',
                'iso_code' => 'GB',
                'phone_code' => '+44',
                'region_id' => $regions->where('code', 'eu')->first()->id,
            ],
            [
                'name' => 'Portugal',
                'iso_code' => 'PT',
                'phone_code' => '+351',
                'region_id' => $regions->where('code', 'eu')->first()->id,
            ],
            [
                'name' => 'Saudi Arabia',
                'iso_code' => 'SA',
                'phone_code' => '+966',
                'region_id' => $regions->where('code', 'me')->first()->id,
            ],
            [
                'name' => 'Egypt',
                'iso_code' => 'EG',
                'phone_code' => '+20',
                'region_id' => $regions->where('code', 'me')->first()->id,
            ],
            [
                'name' => 'Kuwait',
                'iso_code' => 'KW',
                'phone_code' => '+965',
                'region_id' => $regions->where('code', 'me')->first()->id,
            ]
        ];
        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
