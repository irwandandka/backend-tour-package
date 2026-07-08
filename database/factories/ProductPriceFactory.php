<?php

namespace Database\Factories;

use App\Models\ProductDetail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'product_detail_id' => ProductDetail::factory(),
            'level' => 1,
            'purchase_adult' => 100,
            'sales_adult' => 150,
            'purchase_child' => 80,
            'sales_child' => 120,
            'purchase_infant' => 0,
            'sales_infant' => 0,
            'purchase_senior' => 90,
            'sales_senior' => 130,
        ];
    }
}
