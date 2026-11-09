<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataProfil;

class ProfilController extends Controller
{
    /** Halaman Profil: ringkasan akun, keamanan, hak akses, preferensi notifikasi, dan modal Edit Profile */
    public function show()
    {
        return view('unit.profil.show', ['user' => DataProfil::pengguna()]);
    }
}
