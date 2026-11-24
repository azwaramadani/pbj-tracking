
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengadaan', function (Blueprint $table) {
            $table->decimal('Nominal_harga_perkiraan', 15, 2)->nullable();
            $table->decimal('Nominal_harga_akhir', 15, 2)->nullable();
            $table->text('Catatan_pengadaan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pengadaan', function (Blueprint $table) {
            $table->dropColumn([
                'Nominal_harga_perkiraan',
                'Nominal_harga_akhir',
                'Catatan_pengadaan',
            ]);
        });
    }
};
