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
     * Postgres can't implicitly cast bigint -> uuid (there's no meaningful
     * value mapping), so Schema::table(...)->change() fails there even on an
     * empty table. This only ever runs right after personal_access_tokens is
     * created, so the table is always empty - an explicit USING clause that
     * casts through text is safe precisely because no existing rows are ever
     * actually converted.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN tokenable_id TYPE uuid USING (tokenable_id::text::uuid)');

            return;
        }

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->uuid('tokenable_id')->change(); // Ubah menjadi UUID
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN tokenable_id TYPE bigint USING NULL');

            return;
        }

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('tokenable_id')->change(); // Kembali ke integer
        });
    }
};
