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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_id');
            $table->uuid('currency_id');
            $table->uuid('payment_method_id');
            $table->uuid('user_id');
            $table->uuid('status_id');
            $table->double('amount');
            $table->dateTime('payment_date');
            $table->dateTime('due_date');
            $table->string('payment_reference');
            $table->text('notes');
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('status_id')
                ->references('id')
                ->on('statuses')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
