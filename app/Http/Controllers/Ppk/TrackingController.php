<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\TampilTahapTracking;
use App\Support\Ppk\DataTracking;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    use TampilTahapTracking;

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

    /** Detail satu tahap pada Alur Perjalanan (logika bersama PPK dan Super Admin ada di trait TampilTahapTracking) */
    public function tahap(int $id, int $nomor)
    {
        return $this->tampilTahap($id, $nomor, 'ppk', route('ppk.tracking.show', $id), 'tracking');
    }
}
