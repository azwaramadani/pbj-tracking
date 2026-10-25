
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('id', 'Id_role');
            $table->renameColumn('name', 'Nama_role');
            $table->string('Hak_akses')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id', 'Id_user');
            $table->renameColumn('name', 'Nama_lengkap');
            $table->renameColumn('email', 'Username');
            $table->renameColumn('password', 'Password');
            $table->dropColumn('email_verified_at');

            $table->unsignedBigInteger('Id_role')->nullable();
            $table->string('Asal_unit_bagian')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('Id_role')
                ->references('Id_role')
                ->on('roles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['Id_role']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['Id_role', 'Asal_unit_bagian']);
            $table->timestamp('email_verified_at')->nullable();

            $table->renameColumn('Id_user', 'id');
            $table->renameColumn('Nama_lengkap', 'name');
            $table->renameColumn('Username', 'email');
            $table->renameColumn('Password', 'password');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('Hak_akses');
            $table->renameColumn('Id_role', 'id');
            $table->renameColumn('Nama_role', 'name');
        });
    }
};
