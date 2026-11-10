<?php

use App\Http\Controllers\Penerimaan\DashboardController;
use App\Http\Controllers\Penerimaan\DokumentasiController;
use App\Http\Controllers\Penerimaan\NotifikasiController;
use App\Http\Controllers\Penerimaan\ProfilController;
use Illuminate\Support\Facades\Route;

/*
 * Rute role Penerimaan.
 * Semua URL diawali /penerimaan dan semua nama route diawali "penerimaan." (contoh: penerimaan.dashboard),
 * sehingga tidak bentrok dengan role lain. Nanti tambahkan middleware role di group ini.
 */
Route::prefix('penerimaan')->name('penerimaan.')->group(function () {

    // Dashboard Penerimaan: kartu ringkasan + Daftar Penerimaan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dokumentasi Penerimaan (satu halaman, tampilan menyesuaikan status: dijalankan = form dapat diisi, selesai = hanya baca)
    Route::get('/dokumentasi/{id}', [DokumentasiController::class, 'show'])->whereNumber('id')->name('dokumentasi.show');

    // Notifikasi (komponen x-notifikasi-panel dipakai bersama role lain) + aksi tandai dibaca
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/dibaca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.dibaca-semua');
    Route::post('/notifikasi/{id}/dibaca', [NotifikasiController::class, 'tandaiDibaca'])->whereNumber('id')->name('notifikasi.dibaca');

    // Profil (komponen x-profil-panel dan x-profil-modal dipakai bersama role lain)
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
});
