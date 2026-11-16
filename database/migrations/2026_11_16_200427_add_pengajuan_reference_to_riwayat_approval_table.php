
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riwayat_approval', function (Blueprint $table) {
            // Menghubungkan riwayat persetujuan dengan surat pengajuan.
            $table->unsignedBigInteger('Id_pengajuan')->nullable();

            $table->foreign('Id_pengajuan')
                ->references('Id_pengajuan')
                ->on('pengajuan_surat');

            // Pengadaan belum tentu terbentuk saat persetujuan awal.
            $table->unsignedBigInteger('Id_pengadaan')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('riwayat_approval', function (Blueprint $table) {
            $table->dropForeign(['Id_pengajuan']);
            $table->dropColumn('Id_pengajuan');

            // Id_pengadaan tetap nullable agar rollback tidak
            // menghapus atau membuat data baru menjadi tidak valid.
        });
    }
};
