<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(), // UUID for Primary Key
            'name' => $this->faker->country(),
            'iso_code' => strtoupper($this->faker->unique()->lexify('???')), // random ISO Code
            'phone_code' => $this->faker->numerify('+###'),
            'region_id' => Region::factory(),
        ];
    }
}
