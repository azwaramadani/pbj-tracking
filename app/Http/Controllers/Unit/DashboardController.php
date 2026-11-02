<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataPermohonan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** Status yang boleh dipakai pada filter tabel Daftar Pengajuan */
    private const FILTER_STATUS = ['draft', 'menunggu', 'diproses', 'selesai', 'revisi', 'ditolak'];

    /** Dashboard role User Unit: kartu ringkasan dan tabel Daftar Pengajuan */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $status = in_array($status, self::FILTER_STATUS, true) ? $status : '';

        $pengajuan = DataPermohonan::dashboard()
            ->when($q !== '', fn ($koleksi) => $koleksi->filter(fn (array $p) => stripos($p['no_surat'], $q) !== false))
            ->when($status !== '', fn ($koleksi) => $koleksi->where('status', $status))
            ->values();

        // Paginasi 6 baris per halaman; halaman di luar rentang dikembalikan ke rentang yang valid
        $perHalaman = 6;
        $total = $pengajuan->count();
        $totalHalaman = max(1, (int) ceil($total / $perHalaman));
        $halaman = min(max(1, (int) $request->query('page', 1)), $totalHalaman);
        $baris = $pengajuan->forPage($halaman, $perHalaman)->values();

        return view('unit.dashboard', [
            // TODO Backend: hitung dari database (jumlah permohonan unit per status)
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
                'dari'          => $total === 0 ? 0 : ($halaman - 1) * $perHalaman + 1,
                'sampai'        => ($halaman - 1) * $perHalaman + $baris->count(),
                'total'         => $total,
            ],
            'filter'         => ['q' => $q, 'status' => $status],
        ]);
    }
}
