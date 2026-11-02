<?php

namespace App\Http\Controllers\Wadir2;

use App\Http\Controllers\Concerns\KelolaNotifikasi;
use App\Http\Controllers\Controller;

class NotifikasiController extends Controller
{
    use KelolaNotifikasi;

    protected function prefixRute(): string
    {
        return 'wadir2';
    }

    protected function urlDetail(object $notifikasi): string
    {
        return route('wadir2.permohonan.show', $notifikasi->permohonan_id);
    }
}
