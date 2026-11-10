<?php

namespace App\Http\Controllers\Penerimaan;

use App\Http\Controllers\Concerns\KelolaNotifikasi;
use App\Http\Controllers\Controller;

class NotifikasiController extends Controller
{
    use KelolaNotifikasi;

    protected function prefixRute(): string
    {
        return 'penerimaan';
    }

    protected function urlDetail(object $notifikasi): string
    {
        // TODO Backend: arahkan ke penerimaan yang dimaksud notifikasi; data contoh memakai id 801 sebagai contoh
        return route('penerimaan.dokumentasi.show', 801);
    }
}
