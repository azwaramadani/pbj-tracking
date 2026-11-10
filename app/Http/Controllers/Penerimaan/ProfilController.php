<?php

namespace App\Http\Controllers\Penerimaan;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataProfil;

class ProfilController extends Controller
{
    /** Halaman Profil Penerimaan: memakai komponen profil yang sama dengan role lain */
    public function show()
    {
        // TODO Backend: ganti dengan auth()->user() (jabatan, unit, dan hak akses pengguna Penerimaan)
        return view('penerimaan.profil.show', ['user' => DataProfil::pengguna()]);
    }
}
