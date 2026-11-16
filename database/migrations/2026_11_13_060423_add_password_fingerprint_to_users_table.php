<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->char('Password_fingerprint', 64)
                ->nullable()
                ->unique('users_password_fingerprint_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_password_fingerprint_unique');
            $table->dropColumn('Password_fingerprint');
        });
    }
};
