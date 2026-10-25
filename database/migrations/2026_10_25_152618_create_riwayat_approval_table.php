
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_approval', function (Blueprint $table) {
            $table->bigIncrements('Id_approval');

            $table->unsignedBigInteger('Id_pengadaan');
            $table->unsignedBigInteger('Id_user_approver');
            $table->unsignedBigInteger('Id_user_uploader');

            $table->string('Tahapan_approval');
            $table->date('Tanggal_approval');
            $table->text('Catatan')->nullable();
            $table->string('Status_approval');

            $table->foreign('Id_pengadaan')
                ->references('Id_pengadaan')
                ->on('pengadaan');

            $table->foreign('Id_user_approver')
                ->references('Id_user')
                ->on('users');

            $table->foreign('Id_user_uploader')
                ->references('Id_user')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_approval');
    }
};
