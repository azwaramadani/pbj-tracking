<?php

use App\Http\Controllers\Unit\DashboardController;
use App\Http\Controllers\Unit\DokumenController;
use App\Http\Controllers\Unit\PermohonanController;
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

    // Daftar permohonan milik pengguna dengan filter tab, pencarian, dan paginasi
    Route::get('/permohonan', [PermohonanController::class, 'index'])->name('permohonan.index');

    // Formulir usulan pengadaan baru (tampilan saja), dibuka lewat tombol "Buat Permohonan Baru"
    Route::view('/permohonan/buat', 'unit.permohonan.create')->name('permohonan.create');

    // Detail satu permohonan; didefinisikan setelah /permohonan/buat agar tidak bentrok
    Route::get('/permohonan/{id}', [PermohonanController::class, 'show'])->whereNumber('id')->name('permohonan.show');

    // Tracking alur pengadaan; tanpa id menampilkan permohonan terbaru, dengan id menampilkan permohonan tertentu
    Route::get('/tracking/{id?}', [TrackingController::class, 'show'])->whereNumber('id')->name('tracking.index');

    // Daftar dokumen (permohonan, SPK, kontrak) dengan filter jenis, tanggal, status, dan paginasi
    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
});
