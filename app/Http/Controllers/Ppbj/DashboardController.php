<?php

namespace App\Http\Controllers\Ppbj;

use App\Http\Controllers\Concerns\PaginasiDaftar;
use App\Http\Controllers\Controller;
use App\Support\Ppbj\DataPpbj;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use PaginasiDaftar;

    /**
     * Dashboard PPBJ: 4 kartu ringkasan + tabel Daftar Permohonan (jenis pengadaan dan PIC) dengan pencarian, filter, dan paginasi (GET).
     * TODO Backend: ganti DataPpbj::pengadaan() dengan query permohonan yang ditugaskan kepada PPBJ.
     */
    public function index(Request $request)
    {
        $daftar = $this->paginasiDaftar($request, DataPpbj::pengadaan(), ['diproses', 'revisi', 'selesai', 'tinjauan']);

        return view('ppbj.dashboard', [
            // TODO Backend: hitung dari database (jumlah permohonan per status)
            'ringkasan' => [
                ['jumlah' => 8,  'label' => "Menunggu\nTinjauan"],
                ['jumlah' => 12, 'label' => "Kontrak\nAktif"],
                ['jumlah' => 3,  'label' => "Perlu\nDirevisi"],
                ['jumlah' => 2,  'label' => "Upload\nSelesai"],
            ],
        ] + $daftar);
    }
}
