<?php

use App\Http\Controllers\Wadir2\DashboardController;
use App\Http\Controllers\Wadir2\NotifikasiController;
use App\Http\Controllers\Wadir2\PembayaranController;
use App\Http\Controllers\Wadir2\PermohonanController;
use App\Http\Controllers\Wadir2\ProfilController;
use Illuminate\Support\Facades\Route;

/*
 * Rute role Wadir 2.
 * Semua URL diawali /wadir2 dan semua nama route diawali "wadir2." (contoh: wadir2.dashboard),
 * sehingga tidak bentrok dengan role lain. Nanti tambahkan middleware role di group ini.
 */
Route::prefix('wadir2')
    ->name('wadir2.')
    ->middleware(['auth', 'role:Wadir 2'])
    ->group(function () {

    // Dashboard utama Wadir 2 (sama seperti Dashboard User Unit + kolom Unit Pengaju)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Detail Permohonan (dibuka dari tombol "Detail" di Dashboard) lengkap dengan modal Konfirmasi Persetujuan
    Route::get('/permohonan/{id}', [PermohonanController::class, 'show'])->whereNumber('id')->name('permohonan.show');

    // Tab "Pembayaran": kartu ringkasan paket pembayaran + Daftar Permohonan dengan jenis dan nilai pengadaan
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');

    // Persetujuan Paket Pembayaran (dibuka dari tombol "Detail" di tab Pembayaran)
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'show'])->whereNumber('id')->name('pembayaran.show');

    // Notifikasi (komponen x-notifikasi-panel dipakai bersama role lain) + aksi tandai dibaca
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/dibaca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.dibaca-semua');
    Route::post('/notifikasi/{id}/dibaca', [NotifikasiController::class, 'tandaiDibaca'])->whereNumber('id')->name('notifikasi.dibaca');

    // Profil (komponen x-profil-panel dan x-profil-modal dipakai bersama role lain)
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
});
