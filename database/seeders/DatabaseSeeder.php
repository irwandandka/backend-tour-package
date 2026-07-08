<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RegionSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(CitySeeder::class);
        $this->call(StatusSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ProductDetailSeeder::class);
        $this->call(ProductPriceSeeder::class);
        $this->call(ItinerarySeeder::class);
        $this->call(AllotmentSeeder::class);
        $this->call(ReviewSeeder::class);
        $this->call(PaymentMethodSeeder::class);
    }
}
