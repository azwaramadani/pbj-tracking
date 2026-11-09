<?php

namespace App\Http\Controllers\Concerns;

use App\Support\Unit\DataNotifikasi;
use Illuminate\Http\Request;

/**
 * Logika Notifikasi yang sama untuk semua role. Controller role cukup memakai trait ini dan mengisi dua method:
 *   prefixRute() : awalan nama route dan view, contoh "unit" atau "wadir2"
 *   urlDetail()  : alamat halaman detail untuk satu notifikasi (setiap role punya halaman detail sendiri)
 * View yang dirender: "{prefix}.notifikasi.index" (cukup membungkus komponen x-notifikasi-panel).
 */
trait KelolaNotifikasi
{
    /** Tab filter yang diizinkan pada query string ?tab= */
    private const TAB_NOTIFIKASI = ['semua', 'belum-dibaca', 'approval', 'reminder'];

    abstract protected function prefixRute(): string;

    abstract protected function urlDetail(object $notifikasi): string;

    /** Halaman Notifikasi: daftar dengan filter tab (?tab=) di sisi server */
    public function index(Request $request)
    {
        $tab = in_array($request->query('tab'), self::TAB_NOTIFIKASI, true) ? $request->query('tab') : 'semua';

        $semua = DataNotifikasi::semua()->each(fn (object $n) => $n->detail_url = $this->urlDetail($n));

        // Jumlah pada setiap tab dihitung dari seluruh data
        $jumlahTab = [
            'semua'        => $semua->count(),
            'belum-dibaca' => $semua->where('is_read', false)->count(),
            'approval'     => $semua->where('category', 'Approval')->count(),
            'reminder'     => $semua->where('category', 'Reminder')->count(),
        ];

        $notifikasi = match ($tab) {
            'belum-dibaca' => $semua->where('is_read', false),
            'approval'     => $semua->where('category', 'Approval'),
            'reminder'     => $semua->where('category', 'Reminder'),
            default        => $semua,
        };

        return view($this->prefixRute() . '.notifikasi.index', [
            'notifikasi' => $notifikasi->values(),
            'jumlahTab'  => $jumlahTab,
            'tabAktif'   => $tab,
        ]);
    }

    /** Tandai satu notifikasi sebagai dibaca lalu kembali ke halaman sebelumnya */
    public function tandaiDibaca(int $id)
    {
        abort_unless(DataNotifikasi::semua()->contains('id', $id), 404);

        DataNotifikasi::tandaiDibaca($id);

        return back();
    }

    /** Tandai seluruh notifikasi sebagai dibaca */
    public function tandaiSemuaDibaca()
    {
        DataNotifikasi::tandaiSemuaDibaca();

        return back();
    }
}
