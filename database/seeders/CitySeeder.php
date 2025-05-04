<?php

namespace Database\Seeders;

use App\Models\City;
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
        $cities = [
            [
                'name' => 'Tokyo',
                'country_id' => '7775391e-34ef-4a6e-aa27-b150b6c1dd50',
                'postal_code' => '100-0001',
                'latitude' => 35.682839,
                'longitude' => 139.759455,
            ],
            [
                'name' => 'Busan',
                'country_id' => '241c8c57-902d-4712-927a-84411c797849',
                'postal_code' => '601-010',
                'latitude' => 35.1796,
                'longitude' => 129.0756,
            ],
            [
                'name' => 'Beijing',
                'country_id' => '12d896bb-21a0-4256-a2f4-744fb277e77d',
                'postal_code' => '100020',
                'latitude' => 39.9042,
                'longitude' => 116.4074,
            ],
            [
                'name' => 'Bali',
                'country_id' => '72246124-da02-4f27-ba37-507e861041d1',
                'postal_code' => '80361',
                'latitude' => -8.4095,
                'longitude' => 115.1889,
            ],
            [
                'name' => 'Yogyakarta',
                'country_id' => '72246124-da02-4f27-ba37-507e861041d1',
                'postal_code' => '55281',
                'latitude' => -7.7956,
                'longitude' => 110.3695,
            ],
            [
                'name' => 'Bangkok',
                'country_id' => 'cc03580a-cda3-46b1-8f5b-60c4477a635b',
                'postal_code' => '10100',
                'latitude' => 13.7563,
                'longitude' => 100.5018,
            ],
            [
                'name' => 'Chiang Mai',
                'country_id' => 'cc03580a-cda3-46b1-8f5b-60c4477a635b',
                'postal_code' => '50200',
                'latitude' => 18.7884,
                'longitude' => 98.9853,
            ],
            [
                'name' => 'Hanoi',
                'country_id' => '71928d76-1944-4f60-af89-d52456e43c32',
                'postal_code' => '100000',
                'latitude' => 21.0285,
                'longitude' => 105.8542,
            ],
            [
                'name' => 'Ho Chi Minh City',
                'country_id' => '71928d76-1944-4f60-af89-d52456e43c32',
                'postal_code' => '700000',
                'latitude' => 10.8231,
                'longitude' => 106.6297,
            ],
            [
                'name' => 'Kuala Lumpur',
                'country_id' => '0e947ee6-6553-42ba-bae0-7a6ad7862d1b',
                'postal_code' => '50000',
                'latitude' => 3.139,
                'longitude' => 101.6869,
            ],
            [
                'name' => 'Langkawi',
                'country_id' => '0e947ee6-6553-42ba-bae0-7a6ad7862d1b',
                'postal_code' => '07000',
                'latitude' => 6.3185,
                'longitude' => 99.7341,
            ],
            [
                'name' => 'Rome',
                'country_id' => 'b5a2c672-3ff8-461c-a9e9-e93deacbd491',
                'postal_code' => '00100',
                'latitude' => 41.9028,
                'longitude' => 12.4964,
            ],
            [
                'name' => 'London',
                'country_id' => '21e56904-4acf-4e91-b58c-0cf0af6c2aea',
                'postal_code' => 'EC1A 1BB',
                'latitude' => 51.5074,
                'longitude' => -0.1278,
            ],
            [
                'name' => 'Lisbon',
                'country_id' => '36a9512e-f366-4785-9a1a-aecf7c41fc45',
                'postal_code' => '1000-001',
                'latitude' => 38.7223,
                'longitude' => -9.1393,
            ],
            [
                'name' => 'Riyadh',
                'country_id' => '0efdef4c-6080-4525-9ef5-502c29af79a9',
                'postal_code' => '11564',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
            ],
            [
                'name' => 'Cairo',
                'country_id' => '7e4b1e2d-0d33-4600-86ac-ba02f71ad533',
                'postal_code' => '11511',
                'latitude' => 30.0444,
                'longitude' => 31.2357,
            ],
            [
                'name' => 'Kuwait City',
                'country_id' => 'b64efbaf-a4cb-4c5e-8ffb-84c36b52b312',
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
