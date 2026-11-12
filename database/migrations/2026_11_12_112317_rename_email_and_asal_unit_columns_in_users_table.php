
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mengubah nama kolom users sesuai SDD SIMPATIK-PBJ.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('Email', 'Email_user');
            $table->renameColumn('Asal_unit_bagian', 'Asal_unit');
        });
    }

    /**
     * Mengembalikan nama kolom jika migration di-rollback.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('Email_user', 'Email');
            $table->renameColumn('Asal_unit', 'Asal_unit_bagian');
        });
    }
};
