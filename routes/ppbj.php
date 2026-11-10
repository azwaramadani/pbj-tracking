<?php

use App\Http\Controllers\Ppbj\DashboardController;
use App\Http\Controllers\Ppbj\KontrakController;
use App\Http\Controllers\Ppbj\NotifikasiController;
use App\Http\Controllers\Ppbj\PembayaranController;
use App\Http\Controllers\Ppbj\PengadaanController;
use App\Http\Controllers\Ppbj\ProfilController;
use Illuminate\Support\Facades\Route;

/*
 * Rute role PPBJ.
 * Semua URL diawali /ppbj dan semua nama route diawali "ppbj." (contoh: ppbj.dashboard),
 * sehingga tidak bentrok dengan role lain. Nanti tambahkan middleware role di group ini.
 */
Route::prefix('ppbj')->name('ppbj.')->group(function () {

    // Dashboard PPBJ: 4 kartu ringkasan + Daftar Permohonan (jenis pengadaan dan PIC)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Metode Pengadaan (satu halaman, tampilan menyesuaikan status: diproses/selesai = form, revisi = + Permintaan Perbaikan, tinjauan = hanya-baca)
    Route::get('/pengadaan/{id}', [PengadaanController::class, 'show'])->whereNumber('id')->name('pengadaan.show');

    // Lihat Permohonan: memakai ulang tampilan Detail Permohonan (komponen ppk.tracking.tahap.memo) dengan layout PPBJ
    Route::get('/pengadaan/{id}/permohonan', [PengadaanController::class, 'permohonan'])->whereNumber('id')->name('permohonan.show');

    // Kontrak & SPK: dashboard + Detail Kontrak + form Unggah / Edit Kontrak & SPK + Dokumentasi Penerimaan (hanya-baca)
    Route::get('/kontrak', [KontrakController::class, 'index'])->name('kontrak.index');
    Route::get('/kontrak/{id}', [KontrakController::class, 'show'])->whereNumber('id')->name('kontrak.show');
    Route::get('/kontrak/{id}/unggah', [KontrakController::class, 'edit'])->whereNumber('id')->name('kontrak.edit');
    Route::get('/kontrak/{id}/penerimaan', [KontrakController::class, 'penerimaan'])->whereNumber('id')->name('kontrak.penerimaan');

    // Pembayaran: dashboard + satu halaman detail yang berganti tampilan menurut status (unggah, persetujuan paket, selesai)
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'show'])->whereNumber('id')->name('pembayaran.show');

    // Notifikasi (komponen x-notifikasi-panel dipakai bersama role lain) + aksi tandai dibaca
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/dibaca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.dibaca-semua');
    Route::post('/notifikasi/{id}/dibaca', [NotifikasiController::class, 'tandaiDibaca'])->whereNumber('id')->name('notifikasi.dibaca');

    // Profil (komponen x-profil-panel dan x-profil-modal dipakai bersama role lain)
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
});
