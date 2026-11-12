<?php

use App\Http\Controllers\Pp\DashboardController;
use App\Http\Controllers\Pp\NotifikasiController;
use App\Http\Controllers\Pp\PengadaanController;
use App\Http\Controllers\Pp\ProfilController;
use Illuminate\Support\Facades\Route;

/*
 * Rute role PP (Pejabat Pengadaan).
 * Semua URL diawali /pp dan semua nama route diawali "pp." (contoh: pp.dashboard),
 * sehingga tidak bentrok dengan role lain. Nanti tambahkan middleware role di group ini.
 */
Route::prefix('pp')
    ->name('pp.')
    ->middleware(['auth', 'role:PP'])
    ->group(function () {

    // Dashboard PP: kartu ringkasan + Daftar Permohonan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Detail Pengadaan (satu halaman, tampilan menyesuaikan status) + aksi catatan dan konfirmasi review
    Route::get('/pengadaan/{id}', [PengadaanController::class, 'show'])->whereNumber('id')->name('pengadaan.show');

    // Notifikasi (komponen x-notifikasi-panel dipakai bersama role lain) + aksi tandai dibaca
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/dibaca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.dibaca-semua');
    Route::post('/notifikasi/{id}/dibaca', [NotifikasiController::class, 'tandaiDibaca'])->whereNumber('id')->name('notifikasi.dibaca');

    // Profil (komponen x-profil-panel dan x-profil-modal dipakai bersama role lain)
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
});
