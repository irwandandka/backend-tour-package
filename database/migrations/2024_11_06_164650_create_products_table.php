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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('duration')->nullable();
            $table->text('description')->nullable();
            $table->double('price')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->date('date_from');
            $table->date('date_until');
            $table->uuid('user_id');
            $table->uuid('city_id');
            $table->uuid('status_id');
            $table->uuid('category_id');
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('city_id')
                ->references('id')
                ->on('cities')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('status_id')
                ->references('id')
                ->on('statuses')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
