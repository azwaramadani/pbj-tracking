<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Memeriksa apakah pengguna memiliki role yang diizinkan.
     * Super Admin memiliki akses lintas role.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $roleName = $user->role?->Nama_role;

        // Super Admin dapat mengakses seluruh halaman.
        if ($roleName === 'Super Admin') {
            return $next($request);
        }

        // Tolak pengguna jika rolenya tidak sesuai.
        if (!in_array($roleName, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
