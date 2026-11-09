<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Concerns\KelolaNotifikasi;
use App\Http\Controllers\Controller;

class NotifikasiController extends Controller
{
    use KelolaNotifikasi;

    protected function prefixRute(): string
    {
        return 'unit';
    }

    protected function urlDetail(object $notifikasi): string
    {
        return route('unit.permohonan.show', $notifikasi->permohonan_id);
    }
}
