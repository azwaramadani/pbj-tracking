
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan_barang', function (Blueprint $table) {
            $table->bigIncrements('Id_penerimaan');

            $table->unsignedBigInteger('Id_pengadaan');
            $table->unsignedBigInteger('Id_user_perlengkapan');

            $table->string('Status_terima');
            $table->string('Bukti_terima');
            $table->date('Tanggal_terima');

            $table->foreign('Id_pengadaan')
                ->references('Id_pengadaan')
                ->on('pengadaan');

            $table->foreign('Id_user_perlengkapan')
                ->references('Id_user')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan_barang');
    }
};
