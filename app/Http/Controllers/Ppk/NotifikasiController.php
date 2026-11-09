<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Concerns\KelolaNotifikasi;
use App\Http\Controllers\Controller;

class NotifikasiController extends Controller
{
    use KelolaNotifikasi;

    protected function prefixRute(): string
    {
        return 'ppk';
    }

    protected function urlDetail(object $notifikasi): string
    {
        return route('ppk.permohonan.show', $notifikasi->permohonan_id);
    }
}
