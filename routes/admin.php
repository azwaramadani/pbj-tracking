<?php

use App\Http\Controllers\Admin\DaftarController;
use App\Http\Controllers\Admin\DashboardController;
use App\Support\Admin\DataAdmin;
use Illuminate\Support\Facades\Route;

/*
 * Rute role Super Admin.
 * Semua URL diawali /admin dan semua nama route diawali "admin." (contoh: admin.dashboard, admin.pengguna.index),
 * sehingga tidak bentrok dengan role lain. Nanti tambahkan middleware role di group ini.
 */
Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard + Detail (memakai ulang tampilan Detail Tracking milik PPK)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/{id}', [DashboardController::class, 'show'])->whereNumber('id')->name('dashboard.show');
    Route::get('/dashboard/{id}/tahap/{nomor}', [DashboardController::class, 'tahap'])->whereNumber(['id', 'nomor'])->name('dashboard.tahap');

    // 13 halaman daftar (MASTER DATA, TRANSAKSI, SISTEM) memakai satu controller dan satu view generik; yang berbeda hanya datanya
    foreach (DataAdmin::BAGIAN as $bagian) {
        Route::get('/'.$bagian, [DaftarController::class, 'index'])->defaults('bagian', $bagian)->name($bagian.'.index');
    }
});
