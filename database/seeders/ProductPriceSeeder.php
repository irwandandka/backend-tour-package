<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::with(['product_details'])
            ->get();

        DB::transaction(function () use ($products) {
            foreach ($products as $product) {
                foreach ($product->product_details as $detail) {
                    $detail->product_prices()->create([
                        'product_detail_id' => $detail->id,
                        'level' => 1,
                        'purchase_adult' => 100,
                        'sales_adult' => 120,
                        'purchase_child' => 50,
                        'sales_child' => 60,
                        'purchase_infant' => 20,
                        'sales_infant' => 35,
                        'purchase_senior' => 80,
                        'sales_senior' => 100,
                    ]);
                }
            }
        });
    }
}
