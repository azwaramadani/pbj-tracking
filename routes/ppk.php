<?php

use App\Http\Controllers\Ppk\DashboardController;
use App\Http\Controllers\Ppk\MakController;
use App\Http\Controllers\Ppk\NotifikasiController;
use App\Http\Controllers\Ppk\PengadaanController;
use App\Http\Controllers\Ppk\PembayaranController;
use App\Http\Controllers\Ppk\PermohonanController;
use App\Http\Controllers\Ppk\ProfilController;
use App\Http\Controllers\Ppk\TrackingController;
use Illuminate\Support\Facades\Route;

/*
 * Rute role PPK (Pejabat Pembuat Komitmen).
 * Semua URL diawali /ppk dan semua nama route diawali "ppk." (contoh: ppk.dashboard),
 * sehingga tidak bentrok dengan role lain. Nanti tambahkan middleware role di group ini.
 */
Route::prefix('ppk')
    ->name('ppk.')
    ->middleware(['auth', 'role:PPK'])
    ->group(function () {

    // Dashboard PPK: sama seperti Dashboard Wadir 2 (tampilan dipakai ulang)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Detail Permohonan (dibuka dari tombol "Detail" di Dashboard): sama seperti Wadir 2 kecuali kartu Keputusan Persetujuan paling bawah
    Route::get('/permohonan/{id}', [PermohonanController::class, 'show'])->whereNumber('id')->name('permohonan.show');

    // Permohonan MAK: dashboard + detail, sama seperti Wadir 2 (tampilan dipakai ulang)
    Route::get('/permohonan-mak', [MakController::class, 'index'])->name('mak.index');
    Route::get('/permohonan-mak/{id}', [MakController::class, 'show'])->whereNumber('id')->name('mak.show');

    // Pengadaan: dashboard daftar permohonan + Detail Pengadaan (satu halaman, tampilan menyesuaikan status)
    Route::get('/pengadaan', [PengadaanController::class, 'index'])->name('pengadaan.index');
    Route::get('/pengadaan/{id}', [PengadaanController::class, 'show'])->whereNumber('id')->name('pengadaan.show');

    // Pembayaran: daftar + Persetujuan Paket Pembayaran (sama seperti Wadir 2 kecuali area aksi: PPK tanpa tombol "Tentukan Keputusan")
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'show'])->whereNumber('id')->name('pembayaran.show');

    // Tracking: dashboard daftar pengajuan + detail alur perjalanan pengadaan (10 tahap)
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::get('/tracking/{id}', [TrackingController::class, 'show'])->whereNumber('id')->name('tracking.show');

    // Detail tahap 1-10 pada "Alur Perjalanan Pengadaan" (dibuka dengan mengklik tahap yang sudah selesai atau sedang berjalan)
    Route::get('/tracking/{id}/tahap/{nomor}', [TrackingController::class, 'tahap'])->whereNumber(['id', 'nomor'])->name('tracking.tahap');

    // Notifikasi (komponen x-notifikasi-panel dipakai bersama role lain) + aksi tandai dibaca
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/dibaca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.dibaca-semua');
    Route::post('/notifikasi/{id}/dibaca', [NotifikasiController::class, 'tandaiDibaca'])->whereNumber('id')->name('notifikasi.dibaca');

    // Profil (komponen x-profil-panel dan x-profil-modal dipakai bersama role lain)
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
});
