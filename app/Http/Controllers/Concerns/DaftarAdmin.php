<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

/**
 * Menyusun variabel halaman daftar Super Admin (kartu, kepala tabel, baris, pencarian, filter, paginasi) dari satu "bagian"
 * data (lihat App\Support\Admin\DataAdmin). Dipakai bersama oleh Dashboard dan semua halaman master/transaksi/sistem,
 * sehingga seluruhnya dirender oleh satu view generik: resources/views/admin/daftar.blade.php.
 */
trait DaftarAdmin
{
    /** Jumlah baris per halaman */
    private int $perHalamanAdmin = 6;

    /**
     * @param  array  $bagian  satu elemen DataAdmin (judul, deskripsi, kartu, kolom, baris, tambah, ...)
     * TODO Backend: ganti $bagian['baris'] dengan query Eloquent (pencarian LIKE, filter where, paginate()).
     */
    protected function susunDaftar(Request $request, array $bagian): array
    {
        $kolom = $bagian['kolom'];

        // Kolom yang menjadi dasar filter: ditentukan bagian, selain itu kolom "pil" pertama, selain itu kolom pertama
        $kunciFilter = $bagian['filter']
            ?? collect($kolom)->firstWhere('tipe', 'pil')['kunci'] ?? $kolom[0]['kunci'];
        $labelFilter = collect($kolom)->firstWhere('kunci', $kunciFilter)['label'];

        $semua = collect($bagian['baris']);
        $opsiFilter = $semua->pluck($kunciFilter)->unique()->values()->all();

        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $status = in_array($status, $opsiFilter, true) ? $status : '';

        // Pencarian mencocokkan semua kolom teks pada baris
        $hasil = $semua
            ->when($q !== '', fn ($c) => $c->filter(fn (array $b) => stripos(implode(' ', array_map('strval', $b)), $q) !== false))
            ->when($status !== '', fn ($c) => $c->where($kunciFilter, $status))
            ->values();

        $total = $hasil->count();
        $totalHalaman = max(1, (int) ceil($total / $this->perHalamanAdmin));
        $halaman = min(max(1, (int) $request->query('page', 1)), $totalHalaman);
        $baris = $hasil->forPage($halaman, $this->perHalamanAdmin)->values();

        return [
            'pageTitle'       => $bagian['judul'],
            'pageDescription' => $bagian['deskripsi'],
            'summaryCards'    => $bagian['kartu'],
            'tableHeaders'    => $kolom,
            'rows'            => $baris,
            'totalData'       => $total,
            'paginasi'        => [
                'halaman'       => $halaman,
                'total_halaman' => $totalHalaman,
                'dari'          => $total === 0 ? 0 : ($halaman - 1) * $this->perHalamanAdmin + 1,
                'sampai'        => ($halaman - 1) * $this->perHalamanAdmin + $baris->count(),
                'total'         => $total,
            ],
            'filter'          => ['q' => $q, 'status' => $status],
            'filterOpsi'      => $opsiFilter,
            'filterLabel'     => $labelFilter,
            'tampilTambah'    => $bagian['tambah'],
            'menuAktif'       => $bagian['menu'],
            // Isian modal Detail; bila tidak didefinisikan, diambil dari kolom tabel
            'detail'          => isset($bagian['objek']) ? [
                'objek'  => $bagian['objek'],
                'judul'  => $bagian['judul'],
                'fields' => $bagian['detail'] ?? array_map(fn (array $k) => ['label' => $k['label'], 'kunci' => $k['kunci']], $kolom),
            ] : null,
        ];
    }
}
