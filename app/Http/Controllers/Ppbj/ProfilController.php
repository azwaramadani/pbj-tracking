<?php

namespace App\Http\Controllers\Ppbj;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataProfil;

class ProfilController extends Controller
{
    /** Halaman Profil PPBJ: memakai komponen profil yang sama dengan role lain */
    public function show()
    {
        // TODO Backend: ganti dengan auth()->user() (jabatan, unit, dan hak akses pengguna PPBJ)
        return view('ppbj.profil.show', ['user' => DataProfil::pengguna()]);
    }
}
