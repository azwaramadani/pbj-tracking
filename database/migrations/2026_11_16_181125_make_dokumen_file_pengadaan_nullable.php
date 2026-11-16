<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen_file', function (Blueprint $table) {
            // Dokumen dapat diunggah sebelum data pengadaan dibuat.
            $table->unsignedBigInteger('Id_pengadaan')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        if (DB::table('dokumen_file')
            ->whereNull('Id_pengadaan')
            ->exists()) {
            throw new RuntimeException(
                'Tidak dapat rollback karena masih ada dokumen tanpa pengadaan.'
            );
        }

        Schema::table('dokumen_file', function (Blueprint $table) {
            $table->unsignedBigInteger('Id_pengadaan')
                ->nullable(false)
                ->change();
        });
    }
};
