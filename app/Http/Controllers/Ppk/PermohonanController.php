<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Wadir2\PermohonanController as PermohonanWadir2;

/** Detail Permohonan PPK: sama seperti Wadir 2, kecuali kartu Keputusan Persetujuan paling bawah (menampilkan status Wadir 2) */
class PermohonanController extends PermohonanWadir2
{
    protected string $prefixRute = 'ppk';
}
