<?php

use App\Http\Controllers\Admin\DaftarController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Support\Admin\DataAdmin;
use Illuminate\Support\Facades\Route;

/*
 * Rute role Super Admin.
 * Semua URL diawali /admin dan semua nama route diawali "admin." (contoh: admin.dashboard, admin.pengguna.index),
 * sehingga tidak bentrok dengan role lain. Nanti tambahkan middleware role di group ini.
 */
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:Super Admin'])
    ->group(function () {

    // Dashboard + Detail (memakai ulang tampilan Detail Tracking milik PPK)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/{id}', [DashboardController::class, 'show'])->whereNumber('id')->name('dashboard.show');
    Route::get('/dashboard/{id}/tahap/{nomor}', [DashboardController::class, 'tahap'])->whereNumber(['id', 'nomor'])->name('dashboard.tahap');

// Halaman daftar Manajemen Pengguna
Route::get('/pengguna', [DaftarController::class, 'index'])
    ->defaults('bagian', 'pengguna')
    ->name('pengguna.index');
    
    // CRUD khusus Manajemen Pengguna
    Route::get('/pengguna/create', [PenggunaController::class, 'create'])
    ->name('pengguna.create');

    Route::post('/pengguna', [PenggunaController::class, 'store'])
    ->name('pengguna.store');

    Route::put('/pengguna/{id}', [PenggunaController::class, 'update'])
    ->whereNumber('id')
    ->name('pengguna.update');

    Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy'])
    ->whereNumber('id')
    ->name('pengguna.destroy');

    // 13 halaman daftar (MASTER DATA, TRANSAKSI, SISTEM) memakai satu controller dan satu view generik; yang berbeda hanya datanya
    foreach (DataAdmin::BAGIAN as $bagian) {
    // Halaman pengguna akan ditangani secara khusus.
    if ($bagian === 'pengguna') {
        continue;
    }

    Route::get('/'.$bagian, [DaftarController::class, 'index'])
        ->defaults('bagian', $bagian)
        ->name($bagian.'.index');
}
});
