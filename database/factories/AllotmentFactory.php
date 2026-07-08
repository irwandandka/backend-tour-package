<?php

namespace Database\Factories;

use App\Models\ProductDetail;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Allotment>
 */
class AllotmentFactory extends Factory
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
            'transaction_id' => null,
            'period' => now()->format('Ym'),
            'code' => 'ALT-'.strtoupper(Str::random(6)),
        ];
    }
}
