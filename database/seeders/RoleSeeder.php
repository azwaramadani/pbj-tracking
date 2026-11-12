<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'Nama_role' => 'User Unit',
                'Hak_akses' => 'Mengajukan surat dan memantau pengajuan',
            ],
            [
                'Nama_role' => 'Wadir 2',
                'Hak_akses' => 'Melakukan persetujuan pengajuan',
            ],
            [
                'Nama_role' => 'Perencanaan',
                'Hak_akses' => 'Memproses perencanaan pengadaan',
            ],
            [
                'Nama_role' => 'PPBJ',
                'Hak_akses' => 'Mengelola proses pengadaan barang dan jasa',
            ],
            [
                'Nama_role' => 'PP',
                'Hak_akses' => 'Melaksanakan proses pengadaan',
            ],
            [
                'Nama_role' => 'PPK',
                'Hak_akses' => 'Mengelola pengelolaan dan persetujuan pengadaan',
            ],
            [
                'Nama_role' => 'Perlengkapan',
                'Hak_akses' => 'Mengelola penerimaan barang',
            ],
            [
                'Nama_role' => 'Keuangan',
                'Hak_akses' => 'Mengelola pembayaran dan laporan keuangan',
            ],
            [
                'Nama_role' => 'Super Admin',
                'Hak_akses' => 'Mengelola dan mengakses seluruh fitur sistem',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['Nama_role' => $role['Nama_role']],
                [
                    'Hak_akses' => $role['Hak_akses'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
