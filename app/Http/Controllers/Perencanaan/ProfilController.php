<?php

namespace App\Http\Controllers\Perencanaan;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataProfil;

class ProfilController extends Controller
{
    /** Halaman Profil Perencanaan: memakai komponen profil yang sama dengan role lain */
    public function show()
    {
        // TODO Backend: ganti dengan auth()->user() (jabatan, unit, dan hak akses pengguna Perencanaan)
        return view('perencanaan.profil.show', ['user' => DataProfil::pengguna()]);
    }
}
