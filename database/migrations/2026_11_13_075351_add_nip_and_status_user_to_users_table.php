<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('NIP', 30)
                ->nullable()
                ->unique('users_nip_unique');

            $table->string('Status_user', 20)
                ->default('Aktif');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_nip_unique');
            $table->dropColumn(['NIP', 'Status_user']);
        });
    }
};
