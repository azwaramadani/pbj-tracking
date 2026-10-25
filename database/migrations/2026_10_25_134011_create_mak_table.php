
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mak', function (Blueprint $table) {
            $table->bigIncrements('Id_MAK');
            $table->string('Kode_MAK')->unique();
            $table->string('Nama_MAK');
            $table->string('Jenis_anggaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mak');
    }
};
