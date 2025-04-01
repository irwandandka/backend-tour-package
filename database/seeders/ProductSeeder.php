<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $category = Category::first();
        $status = Status::where('code', 'active')->first();
        $city = City::first();
        $user = User::where('email', 'irwndandka@gmail.com')->first();

        $products = [
            [
                'name' => 'Bali Tour Package - 3 Days',
                'slug' => 'bali-tour-pac`kage-3-days',
                'description' => 'Enjoy a 3-day tour of Bali with various exciting destinations.',
                'price' => 1500000,
                'category_id' => null,
                'date_from' => '2024-12-01',
                'date_until' => '2024-12-03',
                'duration' => '3 days 2 nights',
                'thumbnail_image' => 'https://example.com/images/bali-thumbnail.jpg',
                'status_id' => $status->id,
                'city_id' => $city->id,
                'user_id' => $user->id
            ],
            [
                'name' => 'Lombok Tour Package - 5 Days',
                'slug' => 'lombok-tour-package-5-days',
                'description' => 'Complete 5-day tour of Lombok with exotic destinations.',
                'price' => 2500000,
                'category_id' => null,
                'date_from' => '2024-12-10',
                'date_until' => '2024-12-15',
                'duration' => '5 days 4 nights',
                'thumbnail_image' => 'https://example.com/images/lombok-thumbnail.jpg',
                'status_id' => $status->id,
                'city_id' => $city->id,
                'user_id' => $user->id
            ],
            [
                'name' => 'Jakarta City Tour - 2 Days',
                'slug' => 'jakarta-city-tour-2-days',
                'description' => 'Explore the bustling city of Jakarta with guided tours.',
                'price' => 800000,
                'category_id' => null,
                'date_from' => '2024-11-20',
                'date_until' => '2024-11-21',
                'duration' => '2 days 1 night',
                'thumbnail_image' => 'https://example.com/images/jakarta-thumbnail.jpg',
                'status_id' => $status->id,
                'city_id' => $city->id,
                'user_id' => $user->id
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
