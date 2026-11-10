<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\DaftarAdmin;
use App\Http\Controllers\Concerns\TampilTahapTracking;
use App\Http\Controllers\Controller;
use App\Support\Admin\DataAdmin;
use App\Support\Ppk\DataTracking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use DaftarAdmin, TampilTahapTracking;

    /** Dashboard Super Admin: 4 kartu ringkasan + tabel Daftar Dashboard (view generik admin.daftar, tombol Detail menuju Detail Tracking) */
    public function index(Request $request)
    {
        return view('admin.daftar', $this->susunDaftar($request, DataAdmin::dashboard()) + [
            'ruteDetail' => 'admin.dashboard.show',
        ]);
    }

    /**
     * Detail dari Dashboard: tampilan Detail Tracking milik PPK dipakai ulang apa adanya (ppk.tracking.show)
     * dengan layout Super Admin. Tahap pada alur dapat diklik dan membuka detail tahap (tahap "Yang akan datang" tidak bisa dibuka).
     */
    public function show(int $id)
    {
        $pengajuan = DataTracking::cari($id);
        abort_if($pengajuan === null, 404);

        return view('ppk.tracking.show', [
            'pengajuan'       => $pengajuan,
            'layoutRole'      => 'admin',
            'menuAktif'       => 'dashboard',
            'urlKembali'      => route('admin.dashboard'),
            'rutePerTahap'    => 'admin.dashboard.tahap',
        ]);
    }

    /** Detail satu tahap dari Alur Perjalanan: halaman yang sama dengan PPK, dibuka dengan layout Super Admin */
    public function tahap(int $id, int $nomor)
    {
        return $this->tampilTahap($id, $nomor, 'admin', route('admin.dashboard.show', $id), 'dashboard');
    }
}
