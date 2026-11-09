<?php

use App\Http\Controllers\Keuangan\DashboardController;
use App\Http\Controllers\Keuangan\NotifikasiController;
use App\Http\Controllers\Keuangan\PembayaranController;
use App\Http\Controllers\Keuangan\ProfilController;
use Illuminate\Support\Facades\Route;

/*
 * Rute role Keuangan.
 * Semua URL diawali /keuangan dan semua nama route diawali "keuangan." (contoh: keuangan.dashboard),
 * sehingga tidak bentrok dengan role lain. Nanti tambahkan middleware role di group ini.
 */
Route::prefix('keuangan')->name('keuangan.')->group(function () {

    // Dashboard Keuangan: kartu ringkasan + Daftar Penerimaan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Detail Pembayaran (satu halaman, tampilan menyesuaikan status: diproses = form realisasi, selesai = dokumentasi penerimaan)
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'show'])->whereNumber('id')->name('pembayaran.show');

    // Notifikasi (komponen x-notifikasi-panel dipakai bersama role lain) + aksi tandai dibaca
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/dibaca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.dibaca-semua');
    Route::post('/notifikasi/{id}/dibaca', [NotifikasiController::class, 'tandaiDibaca'])->whereNumber('id')->name('notifikasi.dibaca');

    // Profil (komponen x-profil-panel dan x-profil-modal dipakai bersama role lain)
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
});
