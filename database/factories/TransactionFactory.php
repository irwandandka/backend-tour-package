<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
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
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'status_id' => Status::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'currency_id' => Currency::factory(),
            'code' => 'TRX-' . strtoupper(Str::random(8)),
            'quantity' => 1,
            'total_amount' => 1000000,
            'total_amount_base' => 1000000,
            'paid_amount' => 0,
            'booking_date' => now()->toDateString(),
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->safeEmail(),
            'customer_phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'postal_code' => $this->faker->postcode(),
            'notes' => null,
            'date_from' => now()->toDateString(),
            'date_to' => now()->addDays(3)->toDateString(),
        ];
    }
}
