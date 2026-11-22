<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->bigIncrements('Id_notifikasi');

            $table->unsignedBigInteger('Id_user');
            $table->unsignedBigInteger('Id_pengajuan')->nullable();
            $table->unsignedBigInteger('Id_pengadaan')->nullable();

            $table->string('Kategori', 30);
            $table->string('Judul');
            $table->text('Pesan');
            $table->string('Icon', 30)->nullable();

            $table->timestamp('Dibaca_pada')->nullable();

            $table->timestamps();

            $table->foreign('Id_user')
                ->references('Id_user')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('Id_pengajuan')
                ->references('Id_pengajuan')
                ->on('pengajuan_surat')
                ->onDelete('set null');

            $table->foreign('Id_pengadaan')
                ->references('Id_pengadaan')
                ->on('pengadaan')
                ->onDelete('set null');

            $table->index(['Id_user', 'Dibaca_pada']);
            $table->index(['Id_user', 'Kategori']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
