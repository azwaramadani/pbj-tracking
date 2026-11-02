<?php

namespace App\Http\Controllers\Wadir2;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataProfil;

class ProfilController extends Controller
{
    /** Halaman Profil Wadir 2: memakai komponen profil yang sama dengan role lain */
    public function show()
    {
        // TODO Backend: ganti dengan auth()->user() (jabatan, unit, dan hak akses pengguna Wadir 2)
        return view('wadir2.profil.show', ['user' => DataProfil::pengguna()]);
    }
}
