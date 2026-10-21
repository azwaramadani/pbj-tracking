<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataPermohonan;

class DashboardController extends Controller
{
    /** Dashboard role User Unit: ringkasan dan tabel pengajuan terbaru */
    public function index()
    {
        return view('unit.dashboard', [
            'pengajuanTerbaru' => DataPermohonan::dashboard(),
        ]);
    }
}
