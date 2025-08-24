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
            $table->string('code');
            $table->smallInteger('quantity')->default(0);
            $table->double('total_amount')->default(0);
            $table->double('total_amount_base')->default(0);
            $table->date('booking_date');
            $table->string('customer_name', 50)->nullable();
            $table->string('customer_email', 50)->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('notes', 100)->nullable();
            $table->date('date_from');
            $table->date('date_to');
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

            $table
                ->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods')
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
