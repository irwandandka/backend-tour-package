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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_detail_id');
            $table->smallInteger('level');
            $table->double('purchase_adult')->default(0);
            $table->double('sales_adult')->default(0);
            $table->double('purchase_child')->default(0);
            $table->double('sales_child')->default(0);
            $table->double('purchase_infant')->default(0);
            $table->double('sales_infant')->default(0);
            $table->double('purchase_senior')->default(0);
            $table->double('sales_senior')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('product_detail_id')
                ->references('id')
                ->on('product_details')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
