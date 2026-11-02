<?php

use App\Http\Controllers\Unit\DashboardController;
use App\Http\Controllers\Unit\DokumenController;
use App\Http\Controllers\Unit\NotifikasiController;
use App\Http\Controllers\Unit\PermohonanController;
use App\Http\Controllers\Unit\ProfilController;
use App\Http\Controllers\Unit\TrackingController;
use Illuminate\Support\Facades\Route;

/*
 * Rute role User Unit.
 * Semua URL diawali /unit dan semua nama route diawali "unit." (contoh: unit.dashboard),
 * sehingga role lain (mis. wadir, perencanaan, keuangan) bisa punya dashboard sendiri
 * lewat file rute terpisah tanpa bentrok. Nanti tambahkan middleware role di group ini.
 */
Route::prefix('unit')->name('unit.')->group(function () {

    // Halaman utama setelah masuk
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Menu "Permohonan" langsung membuka formulir Buat Permohonan; alamat /permohonan dialihkan ke sana
    Route::redirect('/permohonan', '/unit/permohonan/buat')->name('permohonan.index');

    // Formulir usulan pengadaan baru (tampilan saja), dibuka lewat tombol "Buat Permohonan Baru"
    Route::view('/permohonan/buat', 'unit.permohonan.create')->name('permohonan.create');

    // Detail satu permohonan; didefinisikan setelah /permohonan/buat agar tidak bentrok
    Route::get('/permohonan/{id}', [PermohonanController::class, 'show'])->whereNumber('id')->name('permohonan.show');

    // Formulir Revisi Pengajuan (formulir terisi data lama + kotak Permintaan Perbaikan)
    Route::get('/permohonan/{id}/revisi', [PermohonanController::class, 'revisi'])->whereNumber('id')->name('permohonan.revisi');

    // Tracking alur pengadaan; tanpa id menampilkan permohonan terbaru, dengan id menampilkan permohonan tertentu
    Route::get('/tracking/{id?}', [TrackingController::class, 'show'])->whereNumber('id')->name('tracking.index');

    // Notifikasi: daftar dengan filter tab, plus aksi tandai dibaca (satu / semua)
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/dibaca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.dibaca-semua');
    Route::post('/notifikasi/{id}/dibaca', [NotifikasiController::class, 'tandaiDibaca'])->whereNumber('id')->name('notifikasi.dibaca');

    // Profil pengguna (+ modal Edit Profile yang disembunyikan secara default)
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');

    // Daftar dokumen (permohonan, SPK, kontrak) dengan filter jenis, tanggal, status, dan paginasi
    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
});
