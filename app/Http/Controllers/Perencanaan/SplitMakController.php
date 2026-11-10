<?php

namespace App\Http\Controllers\Perencanaan;

use App\Http\Controllers\Controller;
use App\Support\Perencanaan\DataPerencanaan;

class SplitMakController extends Controller
{
    /** Halaman Split MAK: membagi nilai permohonan ke beberapa Mata Anggaran Kegiatan */
    public function show(int $id)
    {
        $split = DataPerencanaan::split($id);

        abort_if($split === null, 404);

        return view('perencanaan.mak.split', ['split' => $split]);
    }
}
