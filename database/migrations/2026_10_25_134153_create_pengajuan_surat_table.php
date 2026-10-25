
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_surat', function (Blueprint $table) {
            $table->bigIncrements('Id_pengajuan');

            $table->unsignedBigInteger('Id_user_pemohon');

            $table->string('Tujuan_pengadaan');
            $table->string('Status_surat');
            $table->string('Asal_surat');
            $table->date('Tanggal_surat');
            $table->string('Judul_surat');
            $table->string('No_surat')->unique();

            $table->foreign('Id_user_pemohon')
                ->references('Id_user')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surat');
    }
};
