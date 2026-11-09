<?php

namespace App\Http\Controllers\Pp;

use App\Http\Controllers\Concerns\KelolaNotifikasi;
use App\Http\Controllers\Controller;

class NotifikasiController extends Controller
{
    use KelolaNotifikasi;

    protected function prefixRute(): string
    {
        return 'pp';
    }

    protected function urlDetail(object $notifikasi): string
    {
        // TODO Backend: arahkan ke pengadaan yang dimaksud notifikasi; data contoh memakai id 401 sebagai contoh
        return route('pp.pengadaan.show', 401);
    }
}
