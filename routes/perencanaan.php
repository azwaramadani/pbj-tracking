<?php

use App\Http\Controllers\Perencanaan\DashboardController;
use App\Http\Controllers\Perencanaan\NotifikasiController;
use App\Http\Controllers\Perencanaan\PermohonanController;
use App\Http\Controllers\Perencanaan\ProfilController;
use App\Http\Controllers\Perencanaan\SplitMakController;
use Illuminate\Support\Facades\Route;

/*
 * Rute role Perencanaan.
 * Semua URL diawali /perencanaan dan semua nama route diawali "perencanaan." (contoh: perencanaan.dashboard),
 * sehingga tidak bentrok dengan role lain. Nanti tambahkan middleware role di group ini.
 */
Route::prefix('perencanaan')->name('perencanaan.')->group(function () {

    // Dashboard Perencanaan: kartu ringkasan + Daftar Permohonan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Detail Permohonan: form alokasi anggaran (MAK) dan keputusan; tampilan menyesuaikan status (diproses/tinjauan | selesai)
    Route::get('/permohonan/{id}', [PermohonanController::class, 'show'])->whereNumber('id')->name('permohonan.show');

    // Split MAK: pembagian anggaran ke beberapa Mata Anggaran Kegiatan (dibuka dari tombol "Pembagian MAK")
    Route::get('/permohonan/{id}/split-mak', [SplitMakController::class, 'show'])->whereNumber('id')->name('split-mak.show');

    // Notifikasi (komponen x-notifikasi-panel dipakai bersama role lain) + aksi tandai dibaca
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/dibaca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.dibaca-semua');
    Route::post('/notifikasi/{id}/dibaca', [NotifikasiController::class, 'tandaiDibaca'])->whereNumber('id')->name('notifikasi.dibaca');

    // Profil (komponen x-profil-panel dan x-profil-modal dipakai bersama role lain)
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
});
