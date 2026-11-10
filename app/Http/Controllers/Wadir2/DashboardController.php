<?php

namespace App\Http\Controllers\Wadir2;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataPermohonan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected string $prefixRute = 'wadir2';

    /** Status yang boleh dipakai pada filter tabel Daftar Pengajuan */
    private const FILTER_STATUS = ['draft', 'menunggu', 'diproses', 'selesai', 'revisi', 'ditolak'];

    /** Jumlah baris per halaman */
    private const PER_HALAMAN = 6;

    /**
     * Dashboard utama Wadir 2: sama seperti Dashboard User Unit, ditambah kolom "Unit Pengaju".
     * TODO Backend: ganti DataPermohonan::dashboard() dengan query permohonan yang masuk ke Wadir 2 (beserta relasi unit pengaju).
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $status = in_array($status, self::FILTER_STATUS, true) ? $status : '';

        $hasil = DataPermohonan::dashboard()
            ->when($q !== '', fn ($c) => $c->filter(fn (array $p) => stripos($p['no_surat'], $q) !== false))
            ->when($status !== '', fn ($c) => $c->where('status', $status))
            ->values();

        $total = $hasil->count();
        $totalHalaman = max(1, (int) ceil($total / self::PER_HALAMAN));
        $halaman = min(max(1, (int) $request->query('page', 1)), $totalHalaman);
        $baris = $hasil->forPage($halaman, self::PER_HALAMAN)->values();

        return view('wadir2.dashboard', [
            'prefixRute' => $this->prefixRute,
            // TODO Backend: hitung dari database (jumlah permohonan per status)
            'ringkasan' => [
                ['jumlah' => 8,  'label' => "Menunggu\nProses"],
                ['jumlah' => 12, 'label' => "Permohonan\nSelesai"],
                ['jumlah' => 3,  'label' => "Perlu\nDirevisi"],
                ['jumlah' => 2,  'label' => "Permohonan\nDitolak"],
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
}
