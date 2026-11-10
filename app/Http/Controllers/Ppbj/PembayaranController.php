<?php

namespace App\Http\Controllers\Ppbj;

use App\Http\Controllers\Concerns\PaginasiDaftar;
use App\Http\Controllers\Controller;
use App\Support\Keuangan\DataPenerimaan;
use App\Support\Ppbj\DataPpbj;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    use PaginasiDaftar;

    /**
     * Dashboard Pembayaran: 5 kartu ringkasan + tabel Daftar Penerimaan (layout sama dengan Dashboard PPBJ, data dan kolom berbeda).
     * TODO Backend: ganti DataPpbj::pembayaran() dengan query pembayaran yang ditugaskan kepada PPBJ.
     */
    public function index(Request $request)
    {
        $daftar = $this->paginasiDaftar($request, DataPpbj::pembayaran(), ['tinjauan', 'persetujuan', 'diproses', 'pembayaran', 'selesai']);

        return view('ppbj.pembayaran.index', [
            // TODO Backend: hitung dari database (jumlah pembayaran per status)
            'ringkasan' => [
                ['jumlah' => 8, 'label' => "Perlu\nUnggah"],
                ['jumlah' => 1, 'label' => "Menunggu\nPersetujuan"],
                ['jumlah' => 3, 'label' => "Terbitkan\nPembayaran"],
                ['jumlah' => 3, 'label' => "Menunggu\nPembayaran"],
                ['jumlah' => 2, 'label' => "Pengadaan\nSelesai"],
            ],
        ] + $daftar);
    }

    /**
     * Satu alamat, tiga tampilan menurut status pembayaran:
     *   tinjauan / persetujuan   -> Unggah Dokumen Pembayaran (tinjauan: bisa unggah + kirim; persetujuan: hanya-baca, tombol kirim nonaktif)
     *   diproses / pembayaran    -> Persetujuan Paket Pembayaran (diproses: "Terbitkan Perintah" aktif; pembayaran: sudah terbit, nonaktif)
     *   selesai                  -> Pembayaran Selesai (view Keuangan yang sama, memakai layout PPBJ)
     */
    public function show(int $id)
    {
        $pembayaran = DataPpbj::detailPembayaran($id);

        abort_if($pembayaran === null, 404);

        return match ($pembayaran['status']) {
            'tinjauan', 'persetujuan' => view('ppbj.pembayaran.unggah', ['pembayaran' => $pembayaran]),
            'diproses', 'pembayaran'  => view('ppbj.pembayaran.persetujuan', ['pembayaran' => $pembayaran]),
            default                   => view('keuangan.pembayaran.show', [
                'pembayaran' => DataPenerimaan::cari(503),
                'layoutRole' => 'ppbj',
                'menuAktif'  => 'pembayaran',
                'urlKembali' => route('ppbj.pembayaran.index'),
            ]),
        };
    }
}
