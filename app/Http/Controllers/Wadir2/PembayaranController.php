<?php

namespace App\Http\Controllers\Wadir2;

use App\Http\Controllers\Controller;
use App\Support\Wadir2\DataPembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    /** Awalan nama route role pemilik halaman (PPK memakai ulang tampilan ini dengan awalan "ppk") */
    protected string $prefixRute = 'wadir2';

    /** Persetujuan Paket Pembayaran: dokumen paket (kiri) serta informasi paket dan status persetujuan (kanan) */
    public function show(int $id)
    {
        $paket = DataPembayaran::cari($id);

        abort_if($paket === null, 404);

        return view('wadir2.pembayaran.show', ['paket' => $paket, 'prefixRute' => $this->prefixRute]);
    }

    /** Status yang boleh dipakai pada filter tabel Daftar Permohonan */
    private const FILTER_STATUS = ['diproses', 'selesai', 'tinjauan'];

    /** Jumlah baris per halaman */
    private const PER_HALAMAN = 6;

    /** Halaman Pembayaran Wadir 2: kartu ringkasan dan tabel Daftar Permohonan dengan pencarian, filter, dan paginasi (GET) */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $status = in_array($status, self::FILTER_STATUS, true) ? $status : '';

        $hasil = DataPembayaran::semua()
            ->when($q !== '', fn ($c) => $c->filter(fn (array $p) => stripos($p['no_surat'], $q) !== false))
            ->when($status !== '', fn ($c) => $c->where('status', $status))
            ->values();

        $total = $hasil->count();
        $totalHalaman = max(1, (int) ceil($total / self::PER_HALAMAN));
        $halaman = min(max(1, (int) $request->query('page', 1)), $totalHalaman);
        $baris = $hasil->forPage($halaman, self::PER_HALAMAN)->values();

        return view('wadir2.pembayaran.index', [
            'prefixRute' => $this->prefixRute,
            // TODO Backend: hitung dari database (jumlah dokumen per status untuk Wadir 2)
            'ringkasan' => [
                ['jumlah' => 8,  'label' => "Dokumen\nDitugaskan"],
                ['jumlah' => 12, 'label' => "Menunggu\nPersetujuan"],
                ['jumlah' => 2,  'label' => 'Persetujuan Selesai'],
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
