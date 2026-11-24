<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('termin_pengadaan', function (Blueprint $table) {
            $table->bigIncrements('Id_termin_pengadaan');

            $table->unsignedBigInteger('Id_pengadaan');
            $table->unsignedSmallInteger('Termin_ke');
            $table->decimal('Nominal_termin', 15, 2);

            $table->foreign('Id_pengadaan')
                ->references('Id_pengadaan')
                ->on('pengadaan');

            $table->unique(
                ['Id_pengadaan', 'Termin_ke'],
                'termin_pengadaan_pengadaan_termin_unique'
            );
        });

        DB::statement('
            ALTER TABLE termin_pengadaan
            ADD CONSTRAINT termin_pengadaan_termin_ke_check
            CHECK ("Termin_ke" IN (1, 2))
        ');

        DB::statement('
            ALTER TABLE termin_pengadaan
            ADD CONSTRAINT termin_pengadaan_nominal_check
            CHECK ("Nominal_termin" > 0)
        ');

        // Hubungkan pembayaran dengan rencana termin berdasarkan
        // Id_pengadaan dan Termin_ke.
        DB::statement('
            ALTER TABLE pembayaran
            ADD CONSTRAINT pembayaran_termin_pengadaan_fk
            FOREIGN KEY ("Id_pengadaan", "Termin_ke")
            REFERENCES termin_pengadaan ("Id_pengadaan", "Termin_ke")
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE pembayaran
            DROP CONSTRAINT IF EXISTS pembayaran_termin_pengadaan_fk
        ');

        Schema::dropIfExists('termin_pengadaan');
    }
};