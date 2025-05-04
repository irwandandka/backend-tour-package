<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            [
                'name' => 'North America',
                'code' => 'na'
            ],
            [
                'name' => 'South America',
                'code' => 'sa'
            ],
            [
                'name' => 'Europe',
                'code' => 'eu'
            ],
            [
                'name' => 'Asia',
                'code' => 'as'
            ],
            [
                'name' => 'Africa',
                'code' => 'af'
            ],
            [
                'name' => 'Middle East',
                'code' => 'me'
            ],
            [
                'name' => 'Southeast Asia',
                'code' => 'sea'
            ],
        ];
        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}
