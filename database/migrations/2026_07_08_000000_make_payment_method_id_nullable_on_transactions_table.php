<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * transactions.payment_method_id was NOT NULL with no default, but a
     * transaction is created before the customer picks a payment method
     * (that happens later via POST /payment/set-payment-method) — so the
     * very first insert always violated the NOT NULL constraint under
     * strict SQL mode. This makes the column nullable to match how the
     * booking flow actually works. Raw SQL is used instead of
     * Blueprint::change() to avoid adding doctrine/dbal as a dependency.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE transactions MODIFY payment_method_id CHAR(36) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE transactions MODIFY payment_method_id CHAR(36) NOT NULL');
    }
};
