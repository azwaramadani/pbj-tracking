<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Support\Keuangan\DataPenerimaan;

class PembayaranController extends Controller
{
    /**
     * Detail Pembayaran Keuangan: satu halaman untuk dua status (diproses = form realisasi, selesai = dokumentasi penerimaan).
     * TODO Backend: tambahkan aksi POST "Kirim Bukti Penerimaan" (field tanggal_pembayaran, jumlah_dibayar, catatan, bukti_transfer, pernyataan_dana).
     */
    public function show(int $id)
    {
        $pembayaran = DataPenerimaan::cari($id);
        abort_if($pembayaran === null, 404);

        return view('keuangan.pembayaran.show', ['pembayaran' => $pembayaran]);
    }
}
