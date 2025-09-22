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
        Schema::create('cash_banks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invoice_id');
            $table->uuid('currency_id');
            $table->uuid('status_id');
            $table->uuid('payment_method_id');
            $table->string('transaction_number');
            $table->date('transaction_date');
            $table->string('type');
            $table->double('amount')->default(0);
            $table->double('amount_base')->default(0);
            $table->double('rate')->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_banks');
    }
};
