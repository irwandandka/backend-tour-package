<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = $this->faker->unique()->currencyCode();

        return [
            'id' => (string) Str::uuid(),
            'code' => $code,
            'name' => $code,
            'symbol' => $this->faker->randomElement(['$', '€', 'Rp', 'S$', '£']),
            'exchange_rate' => 1,
            'is_active' => true,
        ];
    }
}
