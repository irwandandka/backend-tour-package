<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The `transactions` table migration (2024_11_08_011344) runs before the
     * `payment_methods` table migration (2024_11_08_011391), so its
     * payment_method_id foreign key can't be created inline — the referenced
     * table doesn't exist yet on a fresh migrate. This migration adds it
     * afterwards, once payment_methods is guaranteed to exist. The existence
     * check keeps this safe to run against databases that already have the
     * constraint from before this fix.
     */
    public function up(): void
    {
        $constraintExists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'transactions')
            ->where('CONSTRAINT_NAME', 'transactions_payment_method_id_foreign')
            ->exists();

        if (!$constraintExists) {
            Schema::table('transactions', function (Blueprint $table) {
                $table
                    ->foreign('payment_method_id')
                    ->references('id')
                    ->on('payment_methods')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_payment_method_id_foreign');
        });
    }
};
