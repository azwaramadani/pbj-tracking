<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use App\Support\Pp\DataPengadaan;
use Illuminate\Http\Request;

class PengadaanController extends Controller
{
    /** Status yang boleh dipakai pada filter tabel Daftar Permohonan */
    private const FILTER_STATUS = ['diproses', 'selesai', 'tinjauan', 'ditolak'];

    /** Jumlah baris per halaman */
    private const PER_HALAMAN = 6;

    /**
     * Dashboard Pengadaan PPK: kartu ringkasan dan tabel Daftar Permohonan dengan pencarian, filter, dan paginasi (GET).
     * TODO Backend: ganti DataPengadaan::semua() dengan query pengadaan yang ditugaskan kepada PPK yang sedang login.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $status = in_array($status, self::FILTER_STATUS, true) ? $status : '';

        $hasil = DataPengadaan::semua()
            ->when($q !== '', fn ($c) => $c->filter(fn (array $p) => stripos($p['no_surat'], $q) !== false))
            ->when($status !== '', fn ($c) => $c->where('status', $status))
            ->values();

        $total = $hasil->count();
        $totalHalaman = max(1, (int) ceil($total / self::PER_HALAMAN));
        $halaman = min(max(1, (int) $request->query('page', 1)), $totalHalaman);
        $baris = $hasil->forPage($halaman, self::PER_HALAMAN)->values();

        return view('ppk.pengadaan.index', [
            // TODO Backend: hitung dari database (jumlah dokumen per status untuk PPK)
            'ringkasan' => [
                ['jumlah' => 8,  'label' => "Dokumen\nDitugaskan"],
                ['jumlah' => 12, 'label' => "Menunggu\nTTD"],
                ['jumlah' => 3,  'label' => "Perlu\nDirevisi"],
                ['jumlah' => 2,  'label' => "Selesai\nTinjauan"],
            ],
            'permohonan'      => $baris,
            'totalPermohonan' => $total,
            'paginasi'        => [
                'halaman'       => $halaman,
                'total_halaman' => $totalHalaman,
                'dari'          => $total === 0 ? 0 : ($halaman - 1) * self::PER_HALAMAN + 1,
                'sampai'        => ($halaman - 1) * self::PER_HALAMAN + $baris->count(),
                'total'         => $total,
            ],
            'filter' => ['q' => $q, 'status' => $status],
        ]);
    }

    /**
     * Detail Pengadaan PPK: satu halaman untuk semua status.
     * TODO Backend: tambahkan aksi POST untuk "Kembalikan Untuk Perbaikan" dan "Konfirmasi Review".
     */
    public function show(int $id)
    {
        $pengadaan = DataPengadaan::cari($id);
        abort_if($pengadaan === null, 404);

        return view('ppk.pengadaan.show', ['pengadaan' => $pengadaan]);
    }
}
