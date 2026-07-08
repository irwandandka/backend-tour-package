<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('product_id');
            $table->uuid('status_id');
            $table->uuid('payment_method_id');
            $table->uuid('currency_id');
            $table->string('code');
            $table->smallInteger('quantity')->default(0);
            $table->double('total_amount')->default(0);
            $table->double('total_amount_base')->default(0);
            $table->double('paid_amount')->default(0);
            $table->date('booking_date');
            $table->string('customer_name', 50)->nullable();
            $table->string('customer_email', 50)->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('notes', 100)->nullable();
            $table->date('date_from');
            $table->date('date_to');
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('status_id')
                ->references('id')
                ->on('statuses')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            // payment_method_id foreign key is added later in
            // 2024_11_08_011392_add_payment_method_foreign_to_transactions_table.php
            // because the payment_methods table migration runs after this one.

            $table
                ->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
