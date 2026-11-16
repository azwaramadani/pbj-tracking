<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    /**
     * Form tambah pengguna.
     */
    public function create()
    {
        $roles = Role::orderBy('Id_role')->get();

        return view('admin.pengguna.create', compact('roles'));
    }

    /**
     * Simpan pengguna baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nama_lengkap' => ['required', 'string', 'max:255'],
            'Email_user' => ['required', 'email', 'max:255', 'unique:users,Email_user'],
            'NIP' => ['required', 'string', 'max:30', 'unique:users,NIP'],
            'Asal_unit' => ['nullable', 'string', 'max:255'],
            'Id_role' => ['required', 'exists:roles,Id_role'],
            'Status_user' => ['required', Rule::in(['Aktif', 'Nonaktif'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $user = new User();

                $user->Nama_lengkap = $validated['Nama_lengkap'];
                $user->Email_user = $validated['Email_user'];
                $user->NIP = $validated['NIP'];
                $user->Asal_unit = $validated['Asal_unit'] ?? null;
                $user->Id_role = $validated['Id_role'];
                $user->Status_user = $validated['Status_user'];

                $user->setPasswordWithFingerprint($validated['password']);
                $user->save();
            });
        } catch (QueryException $e) {
            if ($this->isDuplicateFingerprint($e)) {
                return back()
                    ->withInput($request->except(['password', 'password_confirmation']))
                    ->withErrors([
                        'password' => 'Password sudah digunakan oleh akun lain. Silakan gunakan password berbeda.',
                    ]);
            }

            throw $e;
        }

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Perbarui data pengguna.
     */
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'Email_user')->ignore($user->Id_user, 'Id_user'),
            ],
            'nip' => [
                'required',
                'string',
                'max:30',
                Rule::unique('users', 'NIP')->ignore($user->Id_user, 'Id_user'),
            ],
            'unit' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,Id_role'],
            'status' => ['required', Rule::in(['Aktif', 'Nonaktif'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            DB::transaction(function () use ($user, $validated) {
                $user->Nama_lengkap = $validated['nama'];
                $user->Email_user = $validated['email'];
                $user->NIP = $validated['nip'];
                $user->Asal_unit = $validated['unit'] ?? null;
                $user->Id_role = $validated['role_id'];
                $user->Status_user = $validated['status'];

                if (!empty($validated['password'])) {
                    $user->setPasswordWithFingerprint($validated['password']);
                }

                $user->save();
            });
        } catch (QueryException $e) {
            if ($this->isDuplicateFingerprint($e)) {
                return back()
                    ->withInput($request->except(['password', 'password_confirmation']))
                    ->withErrors([
                        'password' => 'Password sudah digunakan oleh akun lain. Silakan gunakan password berbeda.',
                    ]);
            }

            throw $e;
        }

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Hapus pengguna.
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ((int) Auth::id() === (int) $user->Id_user) {
            return back()->withErrors([
                'pengguna' => 'Akun yang sedang digunakan tidak dapat dihapus.',
            ]);
        }

        $user->delete();

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Periksa benturan unique index fingerprint PostgreSQL.
     */
    private function isDuplicateFingerprint(QueryException $e): bool
    {
        return ($e->errorInfo[0] ?? null) === '23505'
            && str_contains(
                $e->getMessage(),
                'users_password_fingerprint_unique'
            );
    }
}
