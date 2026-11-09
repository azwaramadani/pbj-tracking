<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataProfil;

class ProfilController extends Controller
{
    /** Halaman Profil PPK: memakai komponen profil yang sama dengan role lain */
    public function show()
    {
        // TODO Backend: ganti dengan auth()->user() (jabatan, unit, dan hak akses pengguna PPK)
        return view('ppk.profil.show', ['user' => DataProfil::pengguna()]);
    }
}
