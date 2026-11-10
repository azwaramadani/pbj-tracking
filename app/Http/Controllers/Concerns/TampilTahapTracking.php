<?php

namespace App\Http\Controllers\Concerns;

use App\Support\Keuangan\DataPenerimaan;
use App\Support\Ppk\DataTahap;
use App\Support\Ppk\DataTracking;
use App\Support\Wadir2\DataPembayaran;

/**
 * Menampilkan detail satu tahap pada "Alur Perjalanan Pengadaan" (dibuka dengan mengklik tahap di Detail Tracking).
 * Dipakai bersama oleh PPK (Ppk\TrackingController) dan Super Admin (Admin\DashboardController): isi halaman sama,
 * hanya layout/sidebar, menu aktif, dan tombol kembali yang berbeda.
 *
 * Hanya tahap yang sudah selesai atau sedang berjalan yang dapat dibuka; tahap "Yang akan datang" mengembalikan 404.
 * Tahap 9 dan 10 memakai ulang tampilan Persetujuan Paket Pembayaran (Wadir 2) dan Dokumentasi Penerimaan selesai (Keuangan).
 */
trait TampilTahapTracking
{
    protected function tampilTahap(int $id, int $nomor, string $layoutRole, string $urlKembali, string $menuAktif)
    {
        $pengajuan = DataTracking::cari($id);
        abort_if($pengajuan === null || $nomor < 1 || $nomor > 10, 404);

        $state = $pengajuan['alur'][$nomor - 1]['state'];
        abort_if($state === 'menunggu', 404);

        $umum = [
            'pengajuan'  => $pengajuan,
            'tahap'      => $pengajuan['alur'][$nomor - 1],
            'urlKembali' => $urlKembali,
            'menuAktif'  => $menuAktif,
            'layoutRole' => $layoutRole,
        ];

        return match (true) {
            $nomor <= 3 => view('ppk.tracking.tahap.permohonan', $umum + DataTahap::permohonan($nomor, $state)),
            $nomor <= 5 => view('ppk.tracking.tahap.memo', $umum + DataTahap::memo() + ['tampilKeputusan' => $nomor === 5]),
            $nomor === 6 => view('ppk.tracking.tahap.review', $umum + DataTahap::review() + ['state' => $state]),
            $nomor === 7 => view('ppk.tracking.tahap.kontrak', $umum + ['kontrak' => DataTahap::kontrak()]),
            $nomor === 8 => view('ppk.tracking.tahap.penerimaan', $umum + ['penerimaan' => DataTahap::penerimaan()]),
            // Tahap 9: halaman Persetujuan Paket Pembayaran milik Wadir 2 dengan kartu status kedua = PPK (id 203 = Wadir 2 dan Perencanaan sudah disetujui)
            $nomor === 9 => view('wadir2.pembayaran.show', $umum + [
                'prefixRute' => $layoutRole,
                'kartuKedua' => 'kartu_ppk',
                'paket'      => DataPembayaran::cari(203) + ['kartu_ppk' => [
                    'judul' => 'PPK', 'subjudul' => 'Unit Perencanaan & IT', 'status' => 'disetujui',
                    'penanggung_jawab' => 'Dr. Ir. H. Hermawan', 'waktu' => '15 Februari 2026',
                ]],
            ]),
            // Tahap 10: halaman Dokumentasi Penerimaan (keadaan selesai) milik Keuangan (id 503 = pembayaran selesai)
            default => view('keuangan.pembayaran.show', $umum + ['pembayaran' => DataPenerimaan::cari(503)]),
        };
    }
}
