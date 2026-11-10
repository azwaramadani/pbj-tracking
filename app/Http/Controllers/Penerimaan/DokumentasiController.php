<?php

namespace App\Http\Controllers\Penerimaan;

use App\Http\Controllers\Controller;
use App\Support\Penerimaan\DataDokumentasi;

class DokumentasiController extends Controller
{
    /** Dokumentasi Penerimaan: tampilan mengikuti status (dijalankan = form dapat diisi, selesai = hanya baca) */
    public function show(int $id)
    {
        $penerimaan = DataDokumentasi::cari($id);

        abort_if($penerimaan === null, 404);

        return view('penerimaan.dokumentasi.show', ['penerimaan' => $penerimaan]);
    }
}
