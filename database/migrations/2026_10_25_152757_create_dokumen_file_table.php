
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_file', function (Blueprint $table) {
            $table->bigIncrements('Id_dokumen');

            $table->unsignedBigInteger('Id_pengadaan');
            $table->unsignedBigInteger('Id_pengajuan');

            $table->date('Tanggal_dokumen');
            $table->string('File_path');
            $table->string('Kategori_dokumen');

            $table->foreign('Id_pengadaan')
                ->references('Id_pengadaan')
                ->on('pengadaan');

            $table->foreign('Id_pengajuan')
                ->references('Id_pengajuan')
                ->on('pengajuan_surat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_file');
    }
};
