<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Support\Keuangan\DataPenerimaan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** Status yang boleh dipakai pada filter tabel Daftar Penerimaan */
    private const FILTER_STATUS = ['diproses', 'selesai'];

    /** Jumlah baris per halaman */
    private const PER_HALAMAN = 6;

    /**
     * Dashboard Keuangan: dua kartu ringkasan dan tabel Daftar Penerimaan dengan pencarian, filter, dan paginasi (GET).
     * TODO Backend: ganti DataPenerimaan::semua() dengan query pembayaran yang ditugaskan kepada Keuangan.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $status = in_array($status, self::FILTER_STATUS, true) ? $status : '';

        $hasil = DataPenerimaan::semua()
            ->when($q !== '', fn ($c) => $c->filter(fn (array $p) => stripos($p['no_surat'], $q) !== false))
            ->when($status !== '', fn ($c) => $c->where('status', $status))
            ->values();

        $total = $hasil->count();
        $totalHalaman = max(1, (int) ceil($total / self::PER_HALAMAN));
        $halaman = min(max(1, (int) $request->query('page', 1)), $totalHalaman);
        $baris = $hasil->forPage($halaman, self::PER_HALAMAN)->values();

        return view('keuangan.dashboard', [
            // TODO Backend: hitung dari database (jumlah pembayaran per status)
            'ringkasan' => [
                ['jumlah' => 3,  'label' => "Menunggu\nPembayaran"],
                ['jumlah' => 20, 'label' => "Pembayaran\nselesai"],
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
