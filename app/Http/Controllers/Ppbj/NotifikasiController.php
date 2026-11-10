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
        // TODO Backend: arahkan ke pengadaan yang dimaksud notifikasi; data contoh memakai id 901 sebagai contoh
        return route('ppbj.pengadaan.show', 901);
    }
}
