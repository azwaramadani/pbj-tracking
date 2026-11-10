<?php

namespace App\Http\Controllers\Perencanaan;

use App\Http\Controllers\Controller;
use App\Support\Perencanaan\DataPerencanaan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** Status yang boleh dipakai pada filter tabel Daftar Permohonan */
    private const FILTER_STATUS = ['diproses', 'selesai', 'tinjauan'];

    /** Jumlah baris per halaman */
    private const PER_HALAMAN = 6;

    /**
     * Dashboard Perencanaan: 4 kartu ringkasan + tabel Daftar Permohonan dengan pencarian, filter, dan paginasi (GET).
     * TODO Backend: ganti DataPerencanaan::daftar() dengan query permohonan yang masuk ke Perencanaan (beserta relasi unit pengaju).
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $status = in_array($status, self::FILTER_STATUS, true) ? $status : '';

        $hasil = DataPerencanaan::daftar()
            ->when($q !== '', fn ($c) => $c->filter(fn (array $p) => stripos($p['no_surat'], $q) !== false))
            ->when($status !== '', fn ($c) => $c->where('status', $status))
            ->values();

        $total = $hasil->count();
        $totalHalaman = max(1, (int) ceil($total / self::PER_HALAMAN));
        $halaman = min(max(1, (int) $request->query('page', 1)), $totalHalaman);
        $baris = $hasil->forPage($halaman, self::PER_HALAMAN)->values();

        return view('perencanaan.dashboard', [
            // TODO Backend: hitung dari database (jumlah permohonan per status)
            'ringkasan' => [
                ['jumlah' => 8,  'label' => "Menunggu\nMAK"],
                ['jumlah' => 12, 'label' => "MAK\nSelesai"],
                ['jumlah' => 3,  'label' => "Perlu\nDirevisi"],
                ['jumlah' => 2,  'label' => "Permohonan\nDitolak"],
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
}
