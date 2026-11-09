<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use App\Support\Keuangan\DataPenerimaan;
use App\Support\Ppk\DataTahap;
use App\Support\Ppk\DataTracking;
use App\Support\Wadir2\DataPembayaran;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /** Status yang boleh dipakai pada filter tabel Daftar Pengajuan */
    private const FILTER_STATUS = ['revisi', 'diproses', 'selesai', 'draft', 'ditolak', 'pembayaran'];

    /** Jumlah baris per halaman */
    private const PER_HALAMAN = 6;

    /**
     * Dashboard Tracking PPK: kartu ringkasan dan tabel Daftar Pengajuan dengan pencarian, filter, dan paginasi (GET).
     * TODO Backend: ganti DataTracking::daftar() dengan query pengajuan yang dipantau PPK.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $status = in_array($status, self::FILTER_STATUS, true) ? $status : '';

        $hasil = DataTracking::daftar()
            ->when($q !== '', fn ($c) => $c->filter(fn (array $p) => stripos($p['no_surat'], $q) !== false))
            ->when($status !== '', fn ($c) => $c->where('status', $status))
            ->values();

        $total = $hasil->count();
        $totalHalaman = max(1, (int) ceil($total / self::PER_HALAMAN));
        $halaman = min(max(1, (int) $request->query('page', 1)), $totalHalaman);
        $baris = $hasil->forPage($halaman, self::PER_HALAMAN)->values();

        return view('ppk.tracking.index', [
            // TODO Backend: hitung dari database (jumlah per kategori tahap)
            'ringkasan' => [
                ['jumlah' => 8,  'label' => 'Permohonan'],
                ['jumlah' => 12, 'label' => 'Pengadaan'],
                ['jumlah' => 3,  'label' => "Dokumentasi\nBukti"],
                ['jumlah' => 2,  'label' => 'Pembayaran'],
            ],
            'pengajuan'      => $baris,
            'totalPengajuan' => $total,
            'paginasi'       => [
                'halaman'       => $halaman,
                'total_halaman' => $totalHalaman,
                'dari'          => $total === 0 ? 0 : ($halaman - 1) * self::PER_HALAMAN + 1,
                'sampai'        => ($halaman - 1) * self::PER_HALAMAN + $baris->count(),
                'total'         => $total,
            ],
            'filter' => ['q' => $q, 'status' => $status],
        ]);
    }

    /** Detail Tracking: informasi pengadaan (kiri) dan alur perjalanan 10 tahap (kanan) */
    public function show(int $id)
    {
        $pengajuan = DataTracking::cari($id);
        abort_if($pengajuan === null, 404);

        return view('ppk.tracking.show', ['pengajuan' => $pengajuan]);
    }

    /**
     * Detail satu tahap pada "Alur Perjalanan Pengadaan" (dibuka dengan mengklik tahap di Detail Tracking).
     * Hanya tahap yang sudah selesai atau sedang berjalan yang dapat dibuka; tahap "Yang akan datang" mengembalikan 404.
     * Tahap 9 dan 10 memakai ulang tampilan Persetujuan Paket Pembayaran (Wadir 2) dan Dokumentasi Penerimaan selesai (Keuangan).
     */
    public function tahap(int $id, int $nomor)
    {
        $pengajuan = DataTracking::cari($id);
        abort_if($pengajuan === null || $nomor < 1 || $nomor > 10, 404);

        $state = $pengajuan['alur'][$nomor - 1]['state'];
        abort_if($state === 'menunggu', 404);

        // Semua halaman tahap memakai menu "Tracking" dan tombol kembali ke Detail Tracking
        $umum = [
            'pengajuan'  => $pengajuan,
            'tahap'      => $pengajuan['alur'][$nomor - 1],
            'urlKembali' => route('ppk.tracking.show', $id),
            'menuAktif'  => 'tracking',
        ];

        return match (true) {
            $nomor <= 3 => view('ppk.tracking.tahap.permohonan', $umum + DataTahap::permohonan($nomor, $state)),
            $nomor <= 5 => view('ppk.tracking.tahap.memo', $umum + DataTahap::memo() + ['tampilKeputusan' => $nomor === 5]),
            $nomor === 6 => view('ppk.tracking.tahap.review', $umum + DataTahap::review() + ['state' => $state]),
            $nomor === 7 => view('ppk.tracking.tahap.kontrak', $umum + ['kontrak' => DataTahap::kontrak()]),
            $nomor === 8 => view('ppk.tracking.tahap.penerimaan', $umum + ['penerimaan' => DataTahap::penerimaan()]),
            // Tahap 9: halaman Persetujuan Paket Pembayaran milik Wadir 2 dengan kartu status kedua = PPK (id 203 = Wadir 2 dan Perencanaan sudah disetujui)
            $nomor === 9 => view('wadir2.pembayaran.show', $umum + [
                'prefixRute' => 'ppk',
                'kartuKedua' => 'kartu_ppk',
                'paket'      => DataPembayaran::cari(203) + ['kartu_ppk' => [
                    'judul' => 'PPK', 'subjudul' => 'Unit Perencanaan & IT', 'status' => 'disetujui',
                    'penanggung_jawab' => 'Dr. Ir. H. Hermawan', 'waktu' => '15 Februari 2026',
                ]],
            ]),
            // Tahap 10: halaman Dokumentasi Penerimaan (keadaan selesai) milik Keuangan (id 503 = pembayaran selesai)
            default => view('keuangan.pembayaran.show', $umum + [
                'layoutRole' => 'ppk',
                'pembayaran' => DataPenerimaan::cari(503),
            ]),
        };
    }
}
