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
                'Nama_lengkap' => 'Pengujian Wadir 2',
                'Email_user' => 'uji.wadir2@example.test',
                'Id_role' => 2,
            ],
            [
                'Nama_lengkap' => 'Pengujian Perencanaan',
                'Email_user' => 'uji.perencanaan@example.test',
                'Id_role' => 3,
            ],
            [
                'Nama_lengkap' => 'Pengujian PPBJ',
                'Email_user' => 'uji.ppbj@example.test',
                'Id_role' => 4,
            ],
            [
                'Nama_lengkap' => 'Pengujian PP',
                'Email_user' => 'uji.pp@example.test',
                'Id_role' => 5,
            ],
            [
                'Nama_lengkap' => 'Pengujian PPK',
                'Email_user' => 'uji.ppk@example.test',
                'Id_role' => 6,
            ],
            [
                'Nama_lengkap' => 'Pengujian Perlengkapan',
                'Email_user' => 'uji.perlengkapan@example.test',
                'Id_role' => 7,
            ],
            [
                'Nama_lengkap' => 'Pengujian Keuangan',
                'Email_user' => 'uji.keuangan@example.test',
                'Id_role' => 8,
            ],
            [
                'Nama_lengkap' => 'Pengujian Super Admin',
                'Email_user' => 'uji.admin@example.test',
                'Id_role' => 9,
            ],
        ];

        foreach ($akunPengujian as $akun) {
            User::firstOrCreate(
                ['Email_user' => $akun['Email_user']],
                [
                    'Nama_lengkap' => $akun['Nama_lengkap'],
                    'Password' => 'UjiSIMPATIK2026!',
                    'Id_role' => $akun['Id_role'],
                    'Asal_unit' => null,
                ]
            );
        }
    }
}
