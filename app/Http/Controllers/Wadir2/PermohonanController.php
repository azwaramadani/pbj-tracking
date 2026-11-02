<?php

namespace App\Http\Controllers\Wadir2;

use App\Http\Controllers\Controller;
use App\Support\Wadir2\DataDetailPermohonan;

class PermohonanController extends Controller
{
    /** Detail Permohonan untuk Wadir 2: informasi pengadaan, lampiran, identitas pengaju, dan keputusan persetujuan */
    public function show(int $id)
    {
        $permohonan = DataDetailPermohonan::cari($id);

        abort_if($permohonan === null, 404);

        return view('wadir2.permohonan.show', ['permohonan' => $permohonan]);
    }
}
