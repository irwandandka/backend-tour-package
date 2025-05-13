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
        Schema::create('allotments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_detail_id');
            $table->uuid('transaction_id')->nullable();
            $table->string('period', 10);
            $table->string('code', 50);
            $table->smallInteger('day1')->default(0);
            $table->smallInteger('day2')->default(0);
            $table->smallInteger('day3')->default(0);
            $table->smallInteger('day4')->default(0);
            $table->smallInteger('day5')->default(0);
            $table->smallInteger('day6')->default(0);
            $table->smallInteger('day7')->default(0);
            $table->smallInteger('day8')->default(0);
            $table->smallInteger('day9')->default(0);
            $table->smallInteger('day10')->default(0);
            $table->smallInteger('day11')->default(0);
            $table->smallInteger('day12')->default(0);
            $table->smallInteger('day13')->default(0);
            $table->smallInteger('day14')->default(0);
            $table->smallInteger('day15')->default(0);
            $table->smallInteger('day16')->default(0);
            $table->smallInteger('day17')->default(0);
            $table->smallInteger('day18')->default(0);
            $table->smallInteger('day19')->default(0);
            $table->smallInteger('day20')->default(0);
            $table->smallInteger('day21')->default(0);
            $table->smallInteger('day22')->default(0);
            $table->smallInteger('day23')->default(0);
            $table->smallInteger('day24')->default(0);
            $table->smallInteger('day25')->default(0);
            $table->smallInteger('day26')->default(0);
            $table->smallInteger('day27')->default(0);
            $table->smallInteger('day28')->default(0);
            $table->smallInteger('day29')->default(0);
            $table->smallInteger('day30')->default(0);
            $table->smallInteger('day31')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('product_detail_id')
                ->references('id')
                ->on('product_details')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table
                ->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allotments');
    }
};
