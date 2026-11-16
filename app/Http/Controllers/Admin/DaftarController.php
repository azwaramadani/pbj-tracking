<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\DaftarAdmin;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\Admin\DataAdmin;
use Illuminate\Http\Request;

class DaftarController extends Controller
{
    use DaftarAdmin;

    public function index(Request $request, string $bagian)
    {
        $data = DataAdmin::bagian($bagian);

        abort_if($data === null, 404);

        // Khusus Manajemen Pengguna: ambil data langsung dari PostgreSQL.
        // Halaman admin lainnya tetap menggunakan DataAdmin seperti sebelumnya.
        if ($bagian === 'pengguna') {

            $pengguna = User::with('role')
                ->orderBy('Id_user')
                ->get();

            // Memetakan data pengguna untuk ditampilkan pada tabel.
            $data['baris'] = $pengguna->map(function (User $user) {
                return [
                    'id' => $user->Id_user,
                    'nama' => $user->Nama_lengkap,
                    'keterangan' => $user->NIP ?: 'NIP belum tersedia',
                    'email' => $user->Email_user,
                    'nip' => $user->NIP ?? '',
                    'unit' => $user->Asal_unit ?? '-',
                    'role' => $user->role?->Nama_role ?? '-',
                    'role_id' => $user->Id_role,
                    'status' => $user->Status_user ?? 'Nonaktif',
                ];
            })->all();

            // Menghitung kartu ringkasan pengguna.
            $data['kartu'] = [
                [
                    'jumlah' => $pengguna->count(),
                    'label' => 'Total Pengguna',
                ],
                [
                    'jumlah' => $pengguna->where('Status_user', 'Aktif')->count(),
                    'label' => 'Akun Aktif',
                ],
                [
                    'jumlah' => $pengguna->where('Status_user', 'Nonaktif')->count(),
                    'label' => 'Nonaktif',
                ],
                [
                    'jumlah' => Role::count(),
                    'label' => 'Role Terdaftar',
                ],
            ];

            // Mengambil pilihan Role langsung dari tabel roles.
            // Value = Id_role, Label = Nama_role.
            $pilihanRole = Role::orderBy('Id_role')
                ->get(['Id_role', 'Nama_role'])
                ->map(function (Role $role) {
                    return [
                        'value' => (string) $role->Id_role,
                        'label' => $role->Nama_role,
                    ];
                })
                ->all();

            // Mengirim pilihan Role ke dropdown pada modal Edit.
            foreach ($data['detail'] as &$field) {
                if ($field['kunci'] === 'role_id') {
                    $field['opsi'] = $pilihanRole;
                }
            }

            unset($field);
        }

        return view(
            'admin.daftar',
            $this->susunDaftar($request, $data) + [
                'ruteDetail' => null,
            ]
        );
    }
}
