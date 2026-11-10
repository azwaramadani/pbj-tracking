<?php

namespace App\Http\Controllers\Perencanaan;

use App\Http\Controllers\Concerns\KelolaNotifikasi;
use App\Http\Controllers\Controller;

class NotifikasiController extends Controller
{
    use KelolaNotifikasi;

    protected function prefixRute(): string
    {
        return 'perencanaan';
    }

    protected function urlDetail(object $notifikasi): string
    {
        // TODO Backend: arahkan ke permohonan yang dimaksud notifikasi; data contoh memakai id 701 sebagai contoh
        return route('perencanaan.permohonan.show', 701);
    }
}
