<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\DaftarAdmin;
use App\Http\Controllers\Controller;
use App\Support\Admin\DataAdmin;
use Illuminate\Http\Request;

class DaftarController extends Controller
{
    use DaftarAdmin;

    /**
     * Satu controller untuk 13 halaman daftar Super Admin (pengguna, role, unit, mak, pengajuan, pengadaan, dokumen,
     * approval, reminder, penerimaan, pembayaran, notifikasi, audit). Slug "bagian" datang dari defaults() pada route.
     */
    public function index(Request $request, string $bagian)
    {
        $data = DataAdmin::bagian($bagian);
        abort_if($data === null, 404);

        return view('admin.daftar', $this->susunDaftar($request, $data) + ['ruteDetail' => null]);
    }
}
