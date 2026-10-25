
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder', function (Blueprint $table) {
            $table->bigIncrements('Id_reminder');

            $table->unsignedBigInteger('Id_pengadaan');

            $table->string('Jenis_termin');
            $table->string('Status_reminder');
            $table->date('Tanggal_pengiriman');
            $table->date('Tanggal_alarm_H2');

            $table->foreign('Id_pengadaan')
                ->references('Id_pengadaan')
                ->on('pengadaan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder');
    }
};
