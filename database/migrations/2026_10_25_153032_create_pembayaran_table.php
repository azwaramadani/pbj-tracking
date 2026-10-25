
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->bigIncrements('Id_pembayaran');

            $table->unsignedBigInteger('Id_pengadaan');
            $table->unsignedBigInteger('Id_user_keuangan');

            $table->date('Tanggal_bayar');
            $table->string('Status_bayar');
            $table->decimal('Nominal_dibayar', 15, 2);

            $table->foreign('Id_pengadaan')
                ->references('Id_pengadaan')
                ->on('pengadaan');

            $table->foreign('Id_user_keuangan')
                ->references('Id_user')
                ->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
