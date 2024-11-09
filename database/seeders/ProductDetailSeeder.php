<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Retrieve all products to assign details
        $products = Product::all();

        $productDetails = [
            [
                'day' => 'Day 1',
                'title' => 'Airport Pickup and Transfer to Hotel',
                'description' => 'Pickup from airport and transport to hotel for check-in.',
                'schedule_time' => '09:00 AM',
                'location' => 'Ngurah Rai Airport, Bali',
                'activity_image' => 'https://example.com/images/activity1.jpg',
            ],
            [
                'day' => 'Day 2',
                'title' => 'Beach Tour',
                'description' => 'A guided tour of Bali’s famous beaches.',
                'schedule_time' => '10:00 AM',
                'location' => 'Kuta Beach, Bali',
                'activity_image' => 'https://example.com/images/activity2.jpg',
            ],
            [
                'day' => 'Day 3',
                'title' => 'City Tour and Shopping',
                'description' => 'Explore Bali’s city attractions and shopping spots.',
                'schedule_time' => '01:00 PM',
                'location' => 'Denpasar, Bali',
                'activity_image' => 'https://example.com/images/activity3.jpg',
            ],
            // Add more details as needed
        ];

        foreach ($products as $product) {
            foreach ($productDetails as $detail) {
                ProductDetail::create([
                    'product_id' => $product->id,
                    'day' => $detail['day'],
                    'title' => $detail['title'],
                    'description' => $detail['description'],
                    'schedule_time' => $detail['schedule_time'],
                    'location' => $detail['location'],
                    'activity_image' => $detail['activity_image'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
