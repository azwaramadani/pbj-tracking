
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan nominal pengadaan pada pengajuan surat
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->string('Nominal_pengadaan', 255)->nullable();
        });

        // 2. Tambahkan Bruto, Neto, dan Termin_ke pada pembayaran
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('Bruto', 255)->nullable();
            $table->string('Neto', 255)->nullable();
            $table->unsignedSmallInteger('Termin_ke')->nullable();

            $table->unique(
                ['Id_pengadaan', 'Termin_ke'],
                'pembayaran_pengadaan_termin_unique'
            );
        });

        // 3. Tambahkan Termin_ke pada reminder
        Schema::table('reminder', function (Blueprint $table) {
            $table->unsignedSmallInteger('Termin_ke')->nullable();

            $table->unique(
                ['Id_pengadaan', 'Termin_ke'],
                'reminder_pengadaan_termin_unique'
            );
        });

        // 4. Batasi nilai Termin_ke hanya 1 atau 2.
        // NULL sementara diperbolehkan untuk menjaga data lama.
        DB::statement('
            ALTER TABLE pembayaran
            ADD CONSTRAINT pembayaran_termin_ke_check
            CHECK ("Termin_ke" IS NULL OR "Termin_ke" IN (1, 2))
        ');

        DB::statement('
            ALTER TABLE reminder
            ADD CONSTRAINT reminder_termin_ke_check
            CHECK ("Termin_ke" IS NULL OR "Termin_ke" IN (1, 2))
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE pembayaran
            DROP CONSTRAINT IF EXISTS pembayaran_termin_ke_check
        ');

        DB::statement('
            ALTER TABLE reminder
            DROP CONSTRAINT IF EXISTS reminder_termin_ke_check
        ');

        Schema::table('reminder', function (Blueprint $table) {
            $table->dropUnique('reminder_pengadaan_termin_unique');
            $table->dropColumn('Termin_ke');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropUnique('pembayaran_pengadaan_termin_unique');
            $table->dropColumn([
                'Bruto',
                'Neto',
                'Termin_ke'
            ]);
        });

        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->dropColumn('Nominal_pengadaan');
        });
    }
};
