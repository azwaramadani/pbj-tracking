<?php

namespace App\Http\Controllers\Ppbj;

use App\Http\Controllers\Concerns\KelolaNotifikasi;
use App\Http\Controllers\Controller;

class NotifikasiController extends Controller
{
    use KelolaNotifikasi;

    protected function prefixRute(): string
    {
        return 'ppbj';
    }

    protected function urlDetail(object $notifikasi): string
    {
        if ($notifikasi->pengadaan_id) {
            return route(
                'ppbj.pengadaan.show',
                $notifikasi->pengadaan_id
            );
        }

        return route('ppbj.dashboard');
    }
}
