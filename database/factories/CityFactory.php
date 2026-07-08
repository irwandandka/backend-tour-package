<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
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
            'name' => $this->faker->city(),
            'latitude' => number_format($this->faker->latitude(), 8),
            'longitude' => number_format($this->faker->longitude(), 8),
            'postal_code' => $this->faker->postcode(),
            'country_id' => Country::factory(),
            'region_id' => Region::factory()
        ];
    }
}
