<?php

namespace App\Http\Controllers\Perencanaan;

use App\Http\Controllers\Controller;
use App\Support\Perencanaan\DataPerencanaan;

class PermohonanController extends Controller
{
    /** Detail Permohonan Perencanaan: tampilan mengikuti status (diproses/tinjauan = form dapat diisi, selesai = hanya baca) */
    public function show(int $id)
    {
        $permohonan = DataPerencanaan::cari($id);

        abort_if($permohonan === null, 404);

        return view('perencanaan.permohonan.show', [
            'permohonan'   => $permohonan,
            'jenisAnggaran' => DataPerencanaan::jenisAnggaran(),
        ]);
    }
}
