
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengadaan', function (Blueprint $table) {
            $table->string('Vendor')->nullable();
            $table->date('Tanggal_mulai')->nullable();
            $table->date('Target_pengiriman')->nullable();
            $table->date('Tanggal_pengingat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pengadaan', function (Blueprint $table) {
            $table->dropColumn([
                'Vendor',
                'Tanggal_mulai',
                'Target_pengiriman',
                'Tanggal_pengingat',
            ]);
        });
    }
};
