<?php

namespace App\Http\Controllers\Ppbj;

use App\Http\Controllers\Concerns\PaginasiDaftar;
use App\Http\Controllers\Controller;
use App\Support\Penerimaan\DataDokumentasi;
use App\Support\Ppbj\DataPpbj;
use Illuminate\Http\Request;

class KontrakController extends Controller
{
    use PaginasiDaftar;

    /**
     * Dashboard Kontrak & SPK: 3 kartu ringkasan + tabel Daftar Penerimaan (layout sama dengan Dashboard PPBJ, data dan kolom berbeda).
     * TODO Backend: ganti DataPpbj::kontrak() dengan query kontrak/SPK yang ditugaskan kepada PPBJ.
     */
    public function index(Request $request)
    {
        $daftar = $this->paginasiDaftar($request, DataPpbj::kontrak(), ['tinjauan', 'diproses', 'selesai']);

        return view('ppbj.kontrak.index', [
            // TODO Backend: hitung dari database (jumlah kontrak per status)
            'ringkasan' => [
                ['jumlah' => 8,  'label' => "Perlu\nUnggah"],
                ['jumlah' => 12, 'label' => "Kontrak\nAktif"],
                ['jumlah' => 3,  'label' => "Kontrak\nSelesai"],
            ],
        ] + $daftar);
    }

    /** Detail Kontrak: memakai view tahap 7 Tracking PPK dengan layout PPBJ; tombol utama menurut status (lihat view) */
    public function show(int $id)
    {
        $kontrak = DataPpbj::detailKontrak($id);

        abort_if($kontrak === null, 404);

        return view('ppk.tracking.tahap.kontrak', [
            'kontrak'    => $kontrak,
            'layoutRole' => 'ppbj',
            'menuAktif'  => 'kontrak',
            'urlKembali' => route('ppbj.kontrak.index'),
        ]);
    }

    /** Form Unggah / Edit Kontrak & SPK (dibuka dari tombol "Unggah Dokumen" pada kontrak berstatus tinjauan) */
    public function edit(int $id)
    {
        $kontrak = DataPpbj::detailKontrak($id);

        abort_if($kontrak === null, 404);

        return view('ppbj.kontrak.edit', ['kontrak' => $kontrak, 'form' => DataPpbj::formKontrak()]);
    }

    /**
     * "Lihat Penerimaan": Dokumentasi Penerimaan versi hanya-baca (view penerimaan.dokumentasi.show dengan $bacaSaja = true).
     * TODO Backend: ambil penerimaan milik kontrak ini; data contoh memakai id 803 (sudah diterima) atau 801 (barang belum diterima).
     */
    public function penerimaan(int $id)
    {
        $kontrak = DataPpbj::detailKontrak($id);

        abort_if($kontrak === null, 404);

        return view('penerimaan.dokumentasi.show', [
            'penerimaan' => DataDokumentasi::cari($kontrak['status'] === 'selesai' ? 803 : 801),
            'bacaSaja'   => true,
            'layoutRole' => 'ppbj',
            'menuAktif'  => 'kontrak',
            'urlKembali' => route('ppbj.kontrak.show', $id),
        ]);
    }
}
