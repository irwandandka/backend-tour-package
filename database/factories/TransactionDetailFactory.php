<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionDetail>
 */
class TransactionDetailFactory extends Factory
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
            'transaction_id' => Transaction::factory(),
            'product_id' => Product::factory(),
            'product_detail_id' => ProductDetail::factory(),
            'discount_id' => null,
            'user_id' => User::factory(),
            'quantity' => 1,
            'quantity_adult' => 1,
            'quantity_child' => 0,
            'quantity_infant' => 0,
            'quantity_senior' => 0,
            'purchase_adult' => 100,
            'sales_adult' => 150,
            'purchase_child' => 0,
            'sales_child' => 0,
            'purchase_infant' => 0,
            'sales_infant' => 0,
            'purchase_senior' => 0,
            'sales_senior' => 0,
            'purchase_total' => 100,
            'purchase_total_base' => 100,
            'sales_total' => 150,
            'sales_total_base' => 150,
            'purchase_subtotal' => 100,
            'purchase_subtotal_base' => 100,
            'sales_subtotal' => 150,
            'sales_subtotal_base' => 150,
            'discount_amount' => 0,
            'discount_amount_base' => 0,
            'date_from' => now()->toDateString(),
            'date_to' => now()->addDays(3)->toDateString(),
        ];
    }
}
