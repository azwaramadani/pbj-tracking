<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class RoleTestUserSeeder extends Seeder
{
    public function run(): void
    {
        $akunPengujian = [

            [
                'Nama_lengkap' => 'Pengguna Pengujian',
                'Email_user' => 'uji.userunit@example.test',
                'Id_role' => 1,
                'Password' => 'UserUnit_Uji2026!',
            ],

            [
                'Nama_lengkap' => 'Pengujian Wadir 2',
                'Email_user' => 'uji.wadir2@example.test',
                'Id_role' => 2,
                'Password' => 'Wadir2_Uji2026!',
            ],
            [
                'Nama_lengkap' => 'Pengujian Perencanaan',
                'Email_user' => 'uji.perencanaan@example.test',
                'Id_role' => 3,
                'Password' => 'Rencana_Uji2026!',
            ],
            [
                'Nama_lengkap' => 'Pengujian PPBJ',
                'Email_user' => 'uji.ppbj@example.test',
                'Id_role' => 4,
                'Password' => 'PPBJ_Uji2026!',
            ],
            [
                'Nama_lengkap' => 'Pengujian PP',
                'Email_user' => 'uji.pp@example.test',
                'Id_role' => 5,
                'Password' => 'PP_Uji2026!',
            ],
            [
                'Nama_lengkap' => 'Pengujian PPK',
                'Email_user' => 'uji.ppk@example.test',
                'Id_role' => 6,
                'Password' => 'PPK_Uji2026!',
            ],
            [
                'Nama_lengkap' => 'Pengujian Perlengkapan',
                'Email_user' => 'uji.perlengkapan@example.test',
                'Id_role' => 7,
                'Password' => 'Perlengkapan_Uji2026!',
            ],
            [
                'Nama_lengkap' => 'Pengujian Keuangan',
                'Email_user' => 'uji.keuangan@example.test',
                'Id_role' => 8,
                'Password' => 'Keuangan_Uji2026!',
            ],
            [
                'Nama_lengkap' => 'Pengujian Super Admin',
                'Email_user' => 'uji.admin@example.test',
                'Id_role' => 9,
                'Password' => 'Admin_Uji2026!',
            ],
        ];

        foreach ($akunPengujian as $akun) {
            $user = User::firstOrNew([
                'Email_user' => $akun['Email_user'],
            ]);

            $user->Nama_lengkap = $akun['Nama_lengkap'];
            $user->Id_role = $akun['Id_role'];
            $user->Asal_unit = null;

            // Password hanya diatur jika akun baru
            // atau fingerprint akun lama masih kosong.
            if (!$user->exists || empty($user->Password_fingerprint)) {
                $user->setPasswordWithFingerprint($akun['Password']);
            }

            $user->save();
        }
    }
}
