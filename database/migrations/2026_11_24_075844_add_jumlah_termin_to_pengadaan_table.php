<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengadaan', function (Blueprint $table) {
            $table->unsignedSmallInteger('Jumlah_termin')->nullable();
        });

        DB::statement('
            ALTER TABLE pengadaan
            ADD CONSTRAINT pengadaan_jumlah_termin_check
            CHECK ("Jumlah_termin" IS NULL OR "Jumlah_termin" IN (1, 2))
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE pengadaan
            DROP CONSTRAINT IF EXISTS pengadaan_jumlah_termin_check
        ');

        Schema::table('pengadaan', function (Blueprint $table) {
            $table->dropColumn('Jumlah_termin');
        });
    }
};