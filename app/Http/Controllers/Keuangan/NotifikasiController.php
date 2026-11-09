<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Concerns\KelolaNotifikasi;
use App\Http\Controllers\Controller;

class NotifikasiController extends Controller
{
    use KelolaNotifikasi;

    protected function prefixRute(): string
    {
        return 'keuangan';
    }

    protected function urlDetail(object $notifikasi): string
    {
        // TODO Backend: arahkan ke pembayaran yang dimaksud notifikasi; data contoh memakai id 501 sebagai contoh
        return route('keuangan.pembayaran.show', 501);
    }
}
