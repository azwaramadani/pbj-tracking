<?php

namespace App\Http\Controllers\Penerimaan;

use App\Http\Controllers\Controller;
use App\Support\Penerimaan\DataDokumentasi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** Status yang boleh dipakai pada filter tabel Daftar Penerimaan */
    private const FILTER_STATUS = ['dijalankan', 'selesai'];

    /** Jumlah baris per halaman */
    private const PER_HALAMAN = 6;

    /**
     * Dashboard Penerimaan: tiga kartu ringkasan dan tabel Daftar Penerimaan dengan pencarian, filter, dan paginasi (GET).
     * TODO Backend: ganti DataDokumentasi::semua() dengan query penerimaan barang yang ditugaskan kepada role Penerimaan.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $status = in_array($status, self::FILTER_STATUS, true) ? $status : '';

        $hasil = DataDokumentasi::semua()
            ->when($q !== '', fn ($c) => $c->filter(fn (array $p) => stripos($p['no_surat'], $q) !== false))
            ->when($status !== '', fn ($c) => $c->where('status', $status))
            ->values();

        $total = $hasil->count();
        $totalHalaman = max(1, (int) ceil($total / self::PER_HALAMAN));
        $halaman = min(max(1, (int) $request->query('page', 1)), $totalHalaman);
        $baris = $hasil->forPage($halaman, self::PER_HALAMAN)->values();

        return view('penerimaan.dashboard', [
            // TODO Backend: hitung dari database (jumlah penerimaan per status)
            'ringkasan' => [
                ['jumlah' => 8,  'label' => "Menunggu\nPenerimaan"],
                ['jumlah' => 12, 'label' => "SPK\nAktif"],
                ['jumlah' => 3,  'label' => "Penerimaan\nselesai"],
            ],
            'penerimaan'      => $baris,
            'totalPenerimaan' => $total,
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
}
