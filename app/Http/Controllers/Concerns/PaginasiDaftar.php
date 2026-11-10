<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Pencarian (nomor surat), filter status, dan paginasi (GET) untuk tabel daftar dashboard.
 * Dipakai bersama oleh ketiga dashboard PPBJ (Dashboard, Kontrak & SPK, Pembayaran).
 */
trait PaginasiDaftar
{
    /** Jumlah baris per halaman */
    private const BARIS_PER_HALAMAN = 6;

    /** Mengembalikan [baris halaman ini, total, paginasi, filter] siap dikirim ke view */
    protected function paginasiDaftar(Request $request, Collection $semua, array $statusBoleh): array
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $status = in_array($status, $statusBoleh, true) ? $status : '';

        $hasil = $semua
            ->when($q !== '', fn ($c) => $c->filter(fn (array $p) => stripos($p['no_surat'], $q) !== false))
            ->when($status !== '', fn ($c) => $c->where('status', $status))
            ->values();

        $total = $hasil->count();
        $totalHalaman = max(1, (int) ceil($total / self::BARIS_PER_HALAMAN));
        $halaman = min(max(1, (int) $request->query('page', 1)), $totalHalaman);
        $baris = $hasil->forPage($halaman, self::BARIS_PER_HALAMAN)->values();

        return [
            'baris'    => $baris,
            'total'    => $total,
            'paginasi' => [
                'halaman'       => $halaman,
                'total_halaman' => $totalHalaman,
                'dari'          => $total === 0 ? 0 : ($halaman - 1) * self::BARIS_PER_HALAMAN + 1,
                'sampai'        => ($halaman - 1) * self::BARIS_PER_HALAMAN + $baris->count(),
                'total'         => $total,
            ],
            'filter'   => ['q' => $q, 'status' => $status],
        ];
    }
}
