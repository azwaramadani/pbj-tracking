<?php

namespace App\Http\Controllers\Pp;

use App\Http\Controllers\Controller;
use App\Support\Pp\DataPengadaan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** Status yang boleh dipakai pada filter tabel Daftar Permohonan */
    private const FILTER_STATUS = ['diproses', 'selesai', 'tinjauan', 'ditolak'];

    /** Jumlah baris per halaman */
    private const PER_HALAMAN = 6;

    /**
     * Dashboard PP: kartu ringkasan dan tabel Daftar Permohonan dengan pencarian, filter, dan paginasi (GET).
     * TODO Backend: ganti DataPengadaan::semua() dengan query pengadaan yang ditugaskan kepada PP yang sedang login.
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

        return view('pp.dashboard', [
            // TODO Backend: hitung dari database (jumlah dokumen per status untuk PP)
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
}
