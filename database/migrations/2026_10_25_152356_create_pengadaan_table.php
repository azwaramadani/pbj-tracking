
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengadaan', function (Blueprint $table) {
            $table->bigIncrements('Id_pengadaan');

            $table->unsignedBigInteger('Id_PPK_assigned');
            $table->unsignedBigInteger('Id_pengajuan');
            $table->unsignedBigInteger('Id_MAK');

            $table->string('Jenis_pengadaan');
            $table->string('Metode_pengadaan');
            $table->decimal('Nominal_anggaran', 15, 2);
            $table->string('Status_pengadaan');

            $table->foreign('Id_PPK_assigned')
                ->references('Id_user')
                ->on('users');

            $table->foreign('Id_pengajuan')
                ->references('Id_pengajuan')
                ->on('pengajuan_surat');

            $table->foreign('Id_MAK')
                ->references('Id_MAK')
                ->on('mak');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengadaan');
    }
};
