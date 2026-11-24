
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen_file', function (Blueprint $table) {
            $table->unsignedBigInteger('Id_pembayaran')->nullable();

            $table->foreign('Id_pembayaran')
                ->references('Id_pembayaran')
                ->on('pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('dokumen_file', function (Blueprint $table) {
            $table->dropForeign(['Id_pembayaran']);
            $table->dropColumn('Id_pembayaran');
        });
    }
};
