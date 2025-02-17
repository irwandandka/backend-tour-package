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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_id');
            $table->uuid('product_id');
            $table->uuid('discount_id')->nullable();
            $table->uuid('user_id');
            $table->smallInteger('quantity');
            $table->smallInteger('quantity_adult');
            $table->smallInteger('quantity_child');
            $table->smallInteger('quantity_infant');
            $table->smallInteger('quantity_senior');
            $table->double('purchase_adult');
            $table->double('sales_adult');
            $table->double('purchase_child');
            $table->double('sales_child');
            $table->double('purchase_infant');
            $table->double('sales_infant');
            $table->double('purchase_senior');
            $table->double('sales_senior');
            $table->double('purchase_total');
            $table->double('purchase_total_base');
            $table->double('sales_total');
            $table->double('sales_total_base');
            $table->double('purchase_subtotal');
            $table->double('purchase_subtotal_base');
            $table->double('sales_subtotal');
            $table->double('sales_subtotal_base');
            $table->double('discount_amount');
            $table->double('discount_amount_base');
            $table->date('date_from');
            $table->date('date_to');
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('product_detail_id')
                ->references('id')
                ->on('product_details')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('discount_id')
                ->references('id')
                ->on('discounts')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
