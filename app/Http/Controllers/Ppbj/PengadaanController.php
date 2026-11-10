<?php

namespace App\Http\Controllers\Ppbj;

use App\Http\Controllers\Controller;
use App\Support\Ppbj\DataPpbj;
use App\Support\Ppk\DataTahap;

class PengadaanController extends Controller
{
    /** Pencatatan Metode Pengadaan: tampilan mengikuti status (diproses/selesai = form, revisi = + Permintaan Perbaikan, tinjauan = hanya-baca) */
    public function show(int $id)
    {
        $pengadaan = DataPpbj::detailPengadaan($id);

        abort_if($pengadaan === null, 404);

        return view('ppbj.pengadaan.show', ['pengadaan' => $pengadaan]);
    }

    /**
     * Lihat Permohonan: identik dengan Detail Permohonan tahap PPBJ pada Tracking PPK (Informasi, Lampiran, Identitas Pengaju,
     * Keputusan Persetujuan, Form Alokasi Anggaran), jadi memakai ulang view ppk.tracking.tahap.memo dengan layout PPBJ.
     */
    public function permohonan(int $id)
    {
        abort_if(DataPpbj::detailPengadaan($id) === null, 404);

        return view('ppk.tracking.tahap.memo', DataTahap::memo() + [
            'tampilKeputusan' => true,
            'layoutRole'      => 'ppbj',
            'menuAktif'       => 'dashboard',
            'urlKembali'      => route('ppbj.pengadaan.show', $id),
        ]);
    }
}
