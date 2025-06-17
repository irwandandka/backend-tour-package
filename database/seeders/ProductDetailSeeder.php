<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Retrieve all products to assign details
        $products = Product::get();

        $productDetails = [
            [
                'name_en' => 'Standard Room',
                'name_id' => 'Kamar Standar',
                'name_zh' => '标准房',
                'max_pax' => 2,
                'min_adult' => 1,
                'max_adult' => 2,
                'date_from' => '2025-04-01',
                'date_until' => '2025-12-31',
                'is_active' => 1,
                'is_featured' => 1,
                'activity_image' => 'https://pub-cfc04ba1c45649688f85c3bdd738f319.r2.dev/standard-room.jpg',
            ],
            [
                'name_en' => 'Deluxe Room',
                'name_id' => 'Kamar Deluxe',
                'name_zh' => '豪华房',
                'max_pax' => 2,
                'min_adult' => 1,
                'max_adult' => 2,
                'date_from' => '2025-04-01',
                'date_until' => '2025-12-31',
                'is_active' => 1,
                'is_featured' => 1,
                'activity_image' => 'https://pub-cfc04ba1c45649688f85c3bdd738f319.r2.dev/deluxe-room.jpg',
            ],
            [
                'name_en' => 'Family Room',
                'name_id' => 'Kamar Keluarga',
                'name_zh' => '家庭房',
                'max_pax' => 4,
                'min_adult' => 2,
                'max_adult' => 4,
                'date_from' => '2025-04-01',
                'date_until' => '2025-12-31',
                'is_active' => 1,
                'is_featured' => 1,
                'activity_image' => 'https://pub-cfc04ba1c45649688f85c3bdd738f319.r2.dev/family-room.jpg',
            ],
            // Add more details as needed
        ];

        DB::transaction(function () use ($products, $productDetails) {
            foreach ($products as $product) {
                foreach ($productDetails as $detail) {
                    ProductDetail::create(array_merge($detail, ['product_id' => $product->id]));
                }
            }
        });
    }
}
