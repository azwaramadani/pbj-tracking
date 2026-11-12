<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Memproses login pengguna.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $loginData = [
            'Email_user' => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (!Auth::attempt($loginData, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password yang Anda masukkan salah.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $roleName = $user->role?->Nama_role;

        // Tujuan dashboard berdasarkan role pengguna.
        $dashboardRoutes = [
            'User Unit'    => 'unit.dashboard',
            'Wadir 2'      => 'wadir2.dashboard',
            'Perencanaan'  => 'perencanaan.dashboard',
            'PPBJ'         => 'ppbj.dashboard',
            'PP'           => 'pp.dashboard',
            'PPK'          => 'ppk.dashboard',
            'Perlengkapan' => 'penerimaan.dashboard',
            'Keuangan'     => 'keuangan.dashboard',
            'Super Admin'  => 'admin.dashboard',
        ];

        if (!isset($dashboardRoutes[$roleName])) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Role akun Anda belum memiliki dashboard yang tersedia.',
                ]);
        }

        return redirect()->route($dashboardRoutes[$roleName]);
    }

    /**
     * Memproses logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
