<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExchangeRate>
 */
class ExchangeRateFactory extends Factory
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
            'base_currency_id' => Currency::factory(),
            'target_currency_id' => Currency::factory(),
            'exchange_rate' => $this->faker->randomFloat(6, 0.5, 20000),
            'timestamp' => now(),
        ];
    }
}
