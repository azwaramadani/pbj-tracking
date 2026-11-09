<?php

namespace App\Http\Controllers\Pp;

use App\Http\Controllers\Controller;
use App\Support\Pp\DataPengadaan;

class PengadaanController extends Controller
{
    /**
     * Detail Pengadaan PP: satu halaman untuk semua status (diproses, ditolak, selesai, tinjauan).
     * TODO Backend: tambahkan aksi POST untuk menyimpan catatan review (field "catatan") dan konfirmasi review.
     */
    public function show(int $id)
    {
        $pengadaan = DataPengadaan::cari($id);
        abort_if($pengadaan === null, 404);

        return view('pp.pengadaan.show', ['pengadaan' => $pengadaan]);
    }
}
