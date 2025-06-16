<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = Country::all();
        $regions = Region::all();
        $cities = [
            [
                'name' => 'Tokyo',
                'country_id' => $countries->where('iso_code', 'JP')->first()->id,
                'region_id' => $regions->where('code', 'as')->first()->id,
                'postal_code' => '100-0001',
                'latitude' => 35.682839,
                'longitude' => 139.759455,
            ],
            [
                'name' => 'Busan',
                'country_id' => $countries->where('iso_code', 'KR')->first()->id,
                'region_id' => $regions->where('code', 'as')->first()->id,
                'postal_code' => '601-010',
                'latitude' => 35.1796,
                'longitude' => 129.0756,
            ],
            [
                'name' => 'Beijing',
                'country_id' => $countries->where('iso_code', 'CN')->first()->id,
                'region_id' => $regions->where('code', 'as')->first()->id,
                'postal_code' => '100020',
                'latitude' => 39.9042,
                'longitude' => 116.4074,
            ],
            [
                'name' => 'Bali',
                'country_id' => $countries->where('iso_code', 'ID')->first()->id,
                'region_id' => $regions->where('code', 'sea')->first()->id,
                'postal_code' => '80361',
                'latitude' => -8.4095,
                'longitude' => 115.1889,
            ],
            [
                'name' => 'Yogyakarta',
                'country_id' => $countries->where('iso_code', 'ID')->first()->id,
                'region_id' => $regions->where('code', 'sea')->first()->id,
                'postal_code' => '55281',
                'latitude' => -7.7956,
                'longitude' => 110.3695,
            ],
            [
                'name' => 'Bangkok',
                'country_id' => $countries->where('iso_code', 'TH')->first()->id,
                'region_id' => $regions->where('code', 'sea')->first()->id,
                'postal_code' => '10100',
                'latitude' => 13.7563,
                'longitude' => 100.5018,
            ],
            [
                'name' => 'Chiang Mai',
                'country_id' => $countries->where('iso_code', 'TH')->first()->id,
                'region_id' => $regions->where('code', 'sea')->first()->id,
                'postal_code' => '50200',
                'latitude' => 18.7884,
                'longitude' => 98.9853,
            ],
            [
                'name' => 'Hanoi',
                'country_id' => $countries->where('iso_code', 'VN')->first()->id,
                'region_id' => $regions->where('code', 'sea')->first()->id,
                'postal_code' => '100000',
                'latitude' => 21.0285,
                'longitude' => 105.8542,
            ],
            [
                'name' => 'Ho Chi Minh City',
                'country_id' => $countries->where('iso_code', 'VN')->first()->id,
                'region_id' => $regions->where('code', 'sea')->first()->id,
                'postal_code' => '700000',
                'latitude' => 10.8231,
                'longitude' => 106.6297,
            ],
            [
                'name' => 'Kuala Lumpur',
                'country_id' => $countries->where('iso_code', 'MY')->first()->id,
                'region_id' => $regions->where('code', 'sea')->first()->id,
                'postal_code' => '50000',
                'latitude' => 3.139,
                'longitude' => 101.6869,
            ],
            [
                'name' => 'Langkawi',
                'country_id' => $countries->where('iso_code', 'MY')->first()->id,
                'region_id' => $regions->where('code', 'sea')->first()->id,
                'postal_code' => '07000',
                'latitude' => 6.3185,
                'longitude' => 99.7341,
            ],
            [
                'name' => 'Rome',
                'country_id' => $countries->where('iso_code', 'IT')->first()->id,
                'region_id' => $regions->where('code', 'eu')->first()->id,
                'postal_code' => '00100',
                'latitude' => 41.9028,
                'longitude' => 12.4964,
            ],
            [
                'name' => 'London',
                'country_id' => $countries->where('iso_code', 'GB')->first()->id,
                'region_id' => $regions->where('code', 'eu')->first()->id,
                'postal_code' => 'EC1A 1BB',
                'latitude' => 51.5074,
                'longitude' => -0.1278,
            ],
            [
                'name' => 'Lisbon',
                'country_id' => $countries->where('iso_code', 'PT')->first()->id,
                'region_id' => $regions->where('code', 'eu')->first()->id,
                'postal_code' => '1000-001',
                'latitude' => 38.7223,
                'longitude' => -9.1393,
            ],
            [
                'name' => 'Riyadh',
                'country_id' => $countries->where('iso_code', 'SA')->first()->id,
                'region_id' => $regions->where('code', 'me')->first()->id,
                'postal_code' => '11564',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
            ],
            [
                'name' => 'Cairo',
                'country_id' => $countries->where('iso_code', 'EG')->first()->id,
                'region_id' => $regions->where('code', 'me')->first()->id,
                'postal_code' => '11511',
                'latitude' => 30.0444,
                'longitude' => 31.2357,
            ],
            [
                'name' => 'Kuwait City',
                'country_id' => $countries->where('iso_code', 'KW')->first()->id,
                'region_id' => $regions->where('code', 'me')->first()->id,
                'postal_code' => '13001',
                'latitude' => 29.3759,
                'longitude' => 47.9774,
            ]
        ];

        DB::transaction(function () use ($cities) {
            foreach ($cities as $city) {
                City::create($city);
            }
        });
    }
}
