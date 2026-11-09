<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataDokumen;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DokumenController extends Controller
{
    /** Jumlah kartu dokumen per halaman */
    private const PER_HALAMAN = 6;

    /**
     * Halaman Dokumen.
     * Filter dikirim lewat query string: ?jenis=, ?tanggal= (format Y-m), ?status=, dan ?page=.
     * Nilai yang tidak dikenal diabaikan (dianggap "Semua").
     */
    public function index(Request $request)
    {
        $pilihanBulan = DataDokumen::pilihanBulan();

        $jenis = $this->pilih($request->query('jenis'), array_keys(DataDokumen::LABEL_JENIS));
        $status = $this->pilih($request->query('status'), array_keys(DataDokumen::LABEL_STATUS));
        $bulan = $this->pilih($request->query('tanggal'), array_keys($pilihanBulan));
        $halaman = max(1, (int) $request->query('page', 1));

        $hasil = DataDokumen::semua()
            ->when($jenis, fn ($c) => $c->where('jenis', $jenis))
            ->when($status, fn ($c) => $c->where('status', $status))
            ->when($bulan, fn ($c) => $c->where('bulan', $bulan))
            ->values();

        $dokumen = new LengthAwarePaginator(
            $hasil->forPage($halaman, self::PER_HALAMAN)->values(),
            $hasil->count(),
            self::PER_HALAMAN,
            $halaman,
            ['path' => $request->url(), 'query' => $request->except('page')]
        );

        return view('unit.dokumen.index', [
            'dokumen' => $dokumen,
            'pilihanJenis' => DataDokumen::LABEL_JENIS,
            'pilihanBulan' => $pilihanBulan,
            'pilihanStatus' => DataDokumen::LABEL_STATUS,
            'filter' => ['jenis' => $jenis, 'tanggal' => $bulan, 'status' => $status],
        ]);
    }

    /** Kembalikan nilai bila termasuk daftar yang diizinkan; jika tidak, null */
    private function pilih(mixed $nilai, array $diizinkan): ?string
    {
        return is_string($nilai) && in_array($nilai, $diizinkan, true) ? $nilai : null;
    }
}
