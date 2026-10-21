<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataPermohonan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PermohonanController extends Controller
{
    /** Jumlah baris per halaman pada Daftar Permohonan */
    private const PER_HALAMAN = 8;

    /** Kunci filter status yang diizinkan pada query string ?status= */
    private const FILTER_STATUS = ['semua', 'draft', 'menunggu', 'diproses', 'selesai'];

    /**
     * Daftar Permohonan Saya.
     * Mendukung filter tab (?status=), pencarian (?q=), dan paginasi (?page=) di sisi server,
     * sehingga URL dapat dibagikan dan nanti mudah dialihkan ke query Eloquent.
     */
    public function index(Request $request)
    {
        $status = in_array($request->query('status'), self::FILTER_STATUS, true) ? $request->query('status') : 'semua';
        $kataKunci = trim((string) $request->query('q', ''));
        $halaman = max(1, (int) $request->query('page', 1));

        $semua = DataPermohonan::semua();

        // Jumlah per tab dihitung dari seluruh data (tidak terpengaruh pencarian)
        $jumlahTab = ['semua' => $semua->count()];
        foreach (array_slice(self::FILTER_STATUS, 1) as $kunci) {
            $jumlahTab[$kunci] = $semua->where('status', $kunci)->count();
        }

        $hasil = $semua
            ->when($status !== 'semua', fn ($c) => $c->where('status', $status))
            ->when($kataKunci !== '', fn ($c) => $c->filter(
                fn ($p) => Str::contains(Str::lower($p['no_surat'] . ' ' . $p['judul']), Str::lower($kataKunci))
            ))
            ->values();

        $permohonan = new LengthAwarePaginator(
            $hasil->forPage($halaman, self::PER_HALAMAN)->values(),
            $hasil->count(),
            self::PER_HALAMAN,
            $halaman,
            ['path' => $request->url(), 'query' => $request->except('page')]
        );

        return view('unit.permohonan.index', [
            'permohonan' => $permohonan,
            'jumlahTab' => $jumlahTab,
            'statusAktif' => $status,
            'kataKunci' => $kataKunci,
        ]);
    }

    /** Detail Permohonan berdasarkan id */
    public function show(int $id)
    {
        $permohonan = DataPermohonan::cari($id);

        abort_if($permohonan === null, 404);

        return view('unit.permohonan.show', ['permohonan' => $permohonan]);
    }
}
