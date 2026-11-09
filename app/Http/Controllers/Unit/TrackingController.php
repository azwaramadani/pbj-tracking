<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataPermohonan;

class TrackingController extends Controller
{
    /**
     * Tracking Permohonan.
     * Tanpa parameter id, ditampilkan permohonan terbaru pengguna (sementara: pengajuan pertama pada data Dashboard).
     */
    public function show(?int $id = null)
    {
        $id ??= DataPermohonan::dashboard()->first()['id'];

        $tracking = DataPermohonan::tracking($id);

        abort_if($tracking === null, 404);

        return view('unit.tracking.show', $tracking);
    }
}
