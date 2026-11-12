<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Halaman utama diarahkan ke login

use Illuminate\Support\Facades\Auth;

// Halaman utama menyesuaikan status login dan role pengguna.
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $dashboardRoutes = [
        'User Unit'    => 'unit.dashboard',
        'Wadir 2'      => 'wadir2.dashboard',
        'Perencanaan'  => 'perencanaan.dashboard',
        'PPBJ'         => 'ppbj.dashboard',
        'PP'           => 'pp.dashboard',
        'PPK'          => 'ppk.dashboard',
        'Perlengkapan' => 'penerimaan.dashboard',
        'Keuangan'     => 'keuangan.dashboard',
        'Super Admin'  => 'admin.dashboard',
    ];

    $roleName = Auth::user()->role?->Nama_role;

    if (!isset($dashboardRoutes[$roleName])) {
        abort(403, 'Role akun tidak memiliki dashboard.');
    }

    return redirect()->route($dashboardRoutes[$roleName]);
});

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Rute per role
require __DIR__.'/unit.php';
require __DIR__.'/wadir2.php';
require __DIR__.'/pp.php';
require __DIR__.'/keuangan.php';
require __DIR__.'/ppk.php';
require __DIR__.'/perencanaan.php';
require __DIR__.'/penerimaan.php';
require __DIR__.'/ppbj.php';
require __DIR__.'/admin.php';
