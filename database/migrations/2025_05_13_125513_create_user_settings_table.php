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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('language', 5)->default('en');
            $table->string('timezone', 50)->default('UTC');
            $table->boolean('dark_mode_enabled')->default(false);
            $table->boolean('push_notification_enabled')->default(false);
            $table->boolean('email_notification_enabled')->default(false);
            $table->boolean('sms_notification_enabled')->default(false);
            $table->boolean('location_enabled')->default(false);
            $table->boolean('location_sharing_enabled')->default(false);
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('user_settings');
    }
};
