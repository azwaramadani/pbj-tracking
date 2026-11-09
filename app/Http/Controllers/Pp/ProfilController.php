<?php

namespace App\Http\Controllers\Pp;

use App\Http\Controllers\Controller;
use App\Support\Unit\DataProfil;

class ProfilController extends Controller
{
    /** Halaman Profil PP: memakai komponen profil yang sama dengan role lain */
    public function show()
    {
        // TODO Backend: ganti dengan auth()->user() (jabatan, unit, dan hak akses pengguna PP)
        return view('pp.profil.show', ['user' => DataProfil::pengguna()]);
    }
}
