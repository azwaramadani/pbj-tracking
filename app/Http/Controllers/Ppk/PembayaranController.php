<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Wadir2\PembayaranController as PembayaranWadir2;

/**
 * Pembayaran PPK (daftar + Persetujuan Paket Pembayaran): sama seperti Wadir 2, hanya awalan route dan layout yang berganti ke "ppk".
 * Tampilan PPK tidak memiliki tombol "Tentukan Keputusan"; kedua kartu status (Wadir 2 dan Perencanaan) hanya dibaca.
 */
class PembayaranController extends PembayaranWadir2
{
    protected string $prefixRute = 'ppk';
}
