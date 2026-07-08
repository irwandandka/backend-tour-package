<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Currency;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(4, true);

        return [
            'id' => (string) Str::uuid(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(6),
            'description' => $this->faker->paragraph(),
            'thumbnail_image' => null,
            'trip_length' => $this->faker->numberBetween(1, 10),
            'date_from' => now()->toDateString(),
            'date_until' => now()->addYear()->toDateString(),
            'user_id' => User::factory(),
            'city_id' => City::factory(),
            'status_id' => Status::factory(),
            'category_id' => null,
            'purchase_currency_id' => Currency::factory(),
            'sales_currency_id' => Currency::factory(),
        ];
    }
}
