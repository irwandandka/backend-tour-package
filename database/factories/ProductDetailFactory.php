<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductDetail>
 */
class ProductDetailFactory extends Factory
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
            'product_id' => Product::factory(),
            'name_en' => $this->faker->words(3, true),
            'name_id' => null,
            'name_zh' => null,
            'activity_image' => $this->faker->imageUrl(),
            'max_pax' => 10,
            'min_adult' => 1,
            'max_adult' => 10,
            'date_from' => null,
            'date_until' => null,
            'is_active' => true,
            'is_featured' => false,
        ];
    }
}
