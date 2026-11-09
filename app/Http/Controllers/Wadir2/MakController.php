<?php

namespace App\Http\Controllers\Wadir2;

use App\Http\Controllers\Controller;
use App\Support\Wadir2\DataMak;
use Illuminate\Http\Request;

class MakController extends Controller
{
    /** Awalan nama route role pemilik halaman (PPK memakai ulang tampilan ini dengan awalan "ppk") */
    protected string $prefixRute = 'wadir2';
    /** Status yang boleh dipakai pada filter tabel Daftar Permohonan */
    private const FILTER_STATUS = ['tinjau', 'selesai'];

    /** Jumlah baris per halaman */
    private const PER_HALAMAN = 6;

    /**
     * Dashboard Permohonan MAK.
     * TODO Backend: ganti DataMak::daftar() dengan query permohonan MAK yang masuk ke Wadir 2 (beserta relasi unit pengaju).
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $status = in_array($status, self::FILTER_STATUS, true) ? $status : '';

        $hasil = DataMak::daftar()
            ->when($q !== '', fn ($c) => $c->filter(fn (array $p) => stripos($p['no_surat'], $q) !== false))
            ->when($status !== '', fn ($c) => $c->where('status', $status))
            ->values();

        $total = $hasil->count();
        $totalHalaman = max(1, (int) ceil($total / self::PER_HALAMAN));
        $halaman = min(max(1, (int) $request->query('page', 1)), $totalHalaman);
        $baris = $hasil->forPage($halaman, self::PER_HALAMAN)->values();

        return view('wadir2.mak.index', [
            'prefixRute' => $this->prefixRute,
            // TODO Backend: hitung dari database (jumlah permohonan MAK per status)
            'ringkasan' => [
                ['jumlah' => 8,  'label' => "Menunggu\nTinjauan"],
                ['jumlah' => 12, 'label' => "Permohonan\nDisetujui"],
                ['jumlah' => 3,  'label' => "Menunggu\nApprove"],
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

    /** Detail Permohonan MAK; tampilan mengikuti status (tinjau = perlu aksi, selesai = memo sudah terbit) */
    public function show(int $id)
    {
        $permohonan = DataMak::cari($id);
        abort_if($permohonan === null, 404);

        return view('wadir2.mak.show', ['permohonan' => $permohonan, 'prefixRute' => $this->prefixRute]);
    }
}
