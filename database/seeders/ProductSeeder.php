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
use Illuminate\Support\Facades\Log;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::first();
        $status = Status::where('code', 'active')->first();
        $city = City::first();
        $user = User::where('email', 'irwndandka@gmail.com')->first();

        Log::info('Test');
        Log::info($category->id);
        Log::info($status->id);
        Log::info($city->id);

        $products = [
            [
                'name' => 'Bali Tour Package - 3 Days',
                'slug' => 'bali-tour-package-3-days',
                'description' => 'Enjoy a 3-day tour of Bali with various exciting destinations.',
                'price' => 1500000,
                'category_id' => $category->id,
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
                'category_id' => $category->id,
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
                'category_id' => $category->id,
                'date_from' => '2024-11-20',
                'date_until' => '2024-11-21',
                'duration' => '2 days 1 night',
                'thumbnail_image' => 'https://example.com/images/jakarta-thumbnail.jpg',
                'status_id' => $status->id,
                'city_id' => $city->id,
                'user_id' => $user->id
            ],
            [
                'name' => 'Yogyakarta Cultural Tour - 4 Days',
                'slug' => 'yogyakarta-cultural-tour-4-days',
                'description' => "Immerse yourself in Yogyakarta’s rich cultural heritage.",
                'price' => 1800000,
                'category_id' => $category->id,
                'date_from' => '2024-11-25',
                'date_until' => '2024-11-28',
                'duration' => '4 days 3 nights',
                'thumbnail_image' => 'https://example.com/images/yogyakarta-thumbnail.jpg',
                'status_id' => $status->id,
                'city_id' => $city->id,
                'user_id' => $user->id
            ],
            [
                'name' => 'Mount Bromo Adventure - 3 Days',
                'slug' => 'mount-bromo-adventure-3-days',
                'description' => 'Experience the stunning sunrise view from Mount Bromo.',
                'price' => 1300000,
                'category_id' => $category->id,
                'date_from' => '2024-12-05',
                'date_until' => '2024-12-07',
                'duration' => '3 days 2 nights',
                'thumbnail_image' => 'https://example.com/images/bromo-thumbnail.jpg',
                'status_id' => $status->id,
                'city_id' => $city->id,
                'user_id' => $user->id
            ],
            [
                'name' => 'Labuan Bajo Sailing Trip - 4 Days',
                'slug' => 'labuan-bajo-sailing-trip-4-days',
                'description' => 'Discover the Komodo Islands with a memorable sailing adventure.',
                'price' => 3500000,
                'category_id' => $category->id,
                'date_from' => '2024-12-15',
                'date_until' => '2024-12-18',
                'duration' => '4 days 3 nights',
                'thumbnail_image' => 'https://example.com/images/labuan-thumbnail.jpg',
                'status_id' => $status->id,
                'city_id' => $city->id,
                'user_id' => $user->id
            ],
            [
                'name' => 'Bandung Nature and Culinary Tour - 3 Days',
                'slug' => 'bandung-nature-culinary-tour-3-days',
                'description' => 'Enjoy the cool climate of Bandung with nature and culinary tours.',
                'price' => 1000000,
                'category_id' => $category->id,
                'date_from' => '2024-11-29',
                'date_until' => '2024-12-01',
                'duration' => '3 days 2 nights',
                'thumbnail_image' => 'https://example.com/images/bandung-thumbnail.jpg',
                'status_id' => $status->id,
                'city_id' => $city->id,
                'user_id' => $user->id
            ],
            [
                'name' => 'Makassar Historical Tour - 3 Days',
                'slug' => 'makassar-historical-tour-3-days',
                'description' => "Explore Makassar’s historic sites and cultural heritage.",
                'price' => 1200000,
                'category_id' => $category->id,
                'date_from' => '2024-12-08',
                'date_until' => '2024-12-10',
                'duration' => '3 days 2 nights',
                'thumbnail_image' => 'https://example.com/images/makassar-thumbnail.jpg',
                'status_id' => $status->id,
                'city_id' => $city->id,
                'user_id' => $user->id
            ],
            [
                'name' => 'Malang and Batu Adventure - 4 Days',
                'slug' => 'malang-batu-adventure-4-days',
                'description' => 'Enjoy a 4-day tour of Malang and Batu with thrilling activities.',
                'price' => 1700000,
                'category_id' => $category->id,
                'date_from' => '2024-12-20',
                'date_until' => '2024-12-23',
                'duration' => '4 days 3 nights',
                'thumbnail_image' => 'https://example.com/images/malang-thumbnail.jpg',
                'status_id' => $status->id,
                'city_id' => $city->id,
                'user_id' => $user->id
            ],
            [
                'name' => 'Raja Ampat Diving Experience - 5 Days',
                'slug' => 'raja-ampat-diving-experience-5-days',
                'description' => 'Dive into the pristine waters of Raja Ampat for an unforgettable experience.',
                'price' => 5000000,
                'category_id' => $category->id,
                'date_from' => '2024-12-22',
                'date_until' => '2024-12-27',
                'duration' => '5 days 4 nights',
                'thumbnail_image' => 'https://example.com/images/raja-ampat-thumbnail.jpg',
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
