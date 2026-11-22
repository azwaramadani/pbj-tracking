
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel penugasan Pejabat Pengadaan (PP)
        Schema::create('penugasan_pp', function (Blueprint $table) {
            $table->bigIncrements('Id_penugasan_pp');

            $table->unsignedBigInteger('Id_pengadaan');
            $table->unsignedBigInteger('Id_user_pp');

            $table->unique(
                'Id_pengadaan',
                'penugasan_pp_pengadaan_unique'
            );

            $table->foreign('Id_pengadaan')
                ->references('Id_pengadaan')
                ->on('pengadaan')
                ->cascadeOnDelete();

            $table->foreign('Id_user_pp')
                ->references('Id_user')
                ->on('users')
                ->restrictOnDelete();
        });

        // Tabel penugasan Pejabat Pembuat Komitmen (PPK)
        Schema::create('penugasan_ppk', function (Blueprint $table) {
            $table->bigIncrements('Id_penugasan_ppk');

            $table->unsignedBigInteger('Id_pengadaan');
            $table->unsignedBigInteger('Id_user_ppk');

            $table->unique(
                'Id_pengadaan',
                'penugasan_ppk_pengadaan_unique'
            );

            $table->foreign('Id_pengadaan')
                ->references('Id_pengadaan')
                ->on('pengadaan')
                ->cascadeOnDelete();

            $table->foreign('Id_user_ppk')
                ->references('Id_user')
                ->on('users')
                ->restrictOnDelete();
        });

        // Migrasikan penugasan PPK lama ke tabel baru.
        DB::table('pengadaan')
            ->select('Id_pengadaan', 'Id_PPK_assigned')
            ->orderBy('Id_pengadaan')
            ->chunk(500, function ($pengadaans) {
                $data = $pengadaans->map(function ($pengadaan) {
                    return [
                        'Id_pengadaan' => $pengadaan->Id_pengadaan,
                        'Id_user_ppk' => $pengadaan->Id_PPK_assigned,
                    ];
                })->all();

                if (!empty($data)) {
                    DB::table('penugasan_ppk')->insertOrIgnore($data);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_ppk');
        Schema::dropIfExists('penugasan_pp');
    }
};
