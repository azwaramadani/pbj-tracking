<?php

namespace App\Support\Perencanaan;

use Illuminate\Support\Collection;

/**
 * Sumber data contoh (dummy) untuk role Perencanaan.
 *
 * Status permohonan: "diproses" (kuning), "selesai" (hijau), "tinjauan" (abu-abu). Detail "tinjauan" tampil sama seperti "diproses".
 * Id 701-734. Empat baris pertama persis seperti mockup Dashboard Perencanaan.
 * Saat model Eloquent tersedia, ganti isi daftar(), cari(), dan split() dengan query permohonan beserta relasi pengaju, lampiran, memo, dan anggaran.
 */
class DataPerencanaan
{
    /** Daftar permohonan untuk tabel Daftar Permohonan di Dashboard Perencanaan (34 data) */
    public static function daftar(): Collection
    {
        $unit = ['Fakultas Teknik', 'Fakultas Ekonomi', 'Fakultas Hukum', 'Fakultas MIPA', 'Biro Umum'];
        $status = ['diproses', 'selesai', 'tinjauan'];
        $mockup = [
            ['diproses', 'Rp 120.000.000'], ['diproses', 'Rp 320.000.000'],
            ['selesai', 'Rp 400.000.000'],  ['tinjauan', 'Rp 90.000.000'],
        ];

        return collect(range(0, 33))->map(function (int $i) use ($unit, $status, $mockup) {
            // Empat baris pertama sama seperti mockup; sisanya diberi variasi sederhana
            [$st, $nilai] = $mockup[$i] ?? [$status[$i % 3], 'Rp '.number_format(50 + ($i * 13 % 450), 0, ',', '.').'.000.000'];

            return [
                'id'        => 701 + $i,
                'no_surat'  => '021/SPK/PHB-JBT/X/2026',
                'tgl_surat' => '10 Okt 2026',
                'unit'      => $i < 4 ? 'Fakultas Teknik' : $unit[$i % count($unit)],
                'judul'     => 'Pengadaan Komputer Lab',
                'nilai'     => $nilai,
                'status'    => $st,
            ];
        });
    }

    /** Opsi "Jenis Anggaran" pada Form Alokasi Anggaran (MAK) */
    public static function jenisAnggaran(): array
    {
        return ['Rupiah Murni (RM) - Pagu Universitas', 'PNBP - Pagu Fakultas', 'Hibah / Kerja Sama'];
    }

    /** Satu permohonan lengkap untuk halaman Detail Permohonan; null jika tidak ditemukan */
    public static function cari(int $id): ?array
    {
        $dasar = self::daftar()->firstWhere('id', $id);

        if ($dasar === null) {
            return null;
        }

        $selesai = $dasar['status'] === 'selesai';

        return [
            'id'             => $dasar['id'],
            'status'         => $dasar['status'],
            // Isi Informasi Pengadaan dan Lampiran sama seperti mockup untuk semua permohonan contoh
            'nomor_surat'    => '920/UN25.3.1/PBJ/2026',
            'unit_pengaju'   => 'Fakultas Teknik - Jurusan Informatika',
            'judul'          => 'Pengadaan Lab Komputer AI Dasar (Kapasitas 30 Unit PC)',
            'tujuan'         => 'Memfasilitasi mata kuliah praktikum Kecerdasan Buatan dan Pembelajaran Mesin yang memiliki peningkatan jumlah mahasiswa di semester ganjil ini. Alat yang ada saat ini spesifikasinya kurang mumpuni untuk komputasi model AI.',
            'total_anggaran' => 'Rp 450.000.000,00',
            'lampiran'       => [
                ['nama' => 'Surat_Permohonan_FT_AI.pdf',           'ukuran' => '1.2 MB'],
                ['nama' => 'Rincian_Anggaran_Biaya_Komputer.xlsx', 'ukuran' => '485 KB'],
                ['nama' => 'Spesifikasi_Teknis_Komputer_Lab.pdf',  'ukuran' => '2.4 MB'],
            ],
            'pengaju'        => ['nama' => 'Rian Hidayat, S.T.', 'nip' => '198904122020121002', 'inisial' => 'RH'],
            'anggaran'       => [
                'jenis_anggaran' => 'Rupiah Murni (RM) - Pagu Universitas',
                'mata_anggaran'  => '02.01.04.12.001 - Belanja Modal Komputer Lab',
                'jumlah_alokasi' => '120.000.000',
            ],
            // Memo persetujuan Wadir 2 dan PPK hanya ada setelah permohonan selesai
            'memo'           => $selesai ? [
                ['nama' => 'Memo_Peretujuan_Wadir 2.pdf', 'ukuran' => '1.2 MB'],
                ['nama' => 'Memo_Peretujuan_PPK.pdf',     'ukuran' => '1.2 MB'],
            ] : [],
        ];
    }

    /** Opsi Mata Anggaran Kegiatan untuk halaman Split MAK; 'kategori' dipakai pada label dokumen turunan di diagram */
    public static function opsiMak(): array
    {
        return [
            ['kode' => '02.01.04.12.001', 'label' => '02.01.04.12.001 - Belanja Modal Komputer (Pagu Operasional)', 'kategori' => 'Operasional'],
            ['kode' => '02.01.04.12.005', 'label' => '02.01.04.12.005 - Pagu Pemeliharaan Rutin TIK',             'kategori' => 'Pemeliharaan'],
            ['kode' => '02.01.04.12.009', 'label' => '02.01.04.12.009 - Pagu Pengembangan Laboratorium',           'kategori' => 'Pengembangan'],
        ];
    }

    /** Data halaman Split MAK untuk satu permohonan; null jika tidak ditemukan */
    public static function split(int $id): ?array
    {
        if (self::daftar()->firstWhere('id', $id) === null) {
            return null;
        }

        return [
            'id'          => $id,
            'status'      => 'ditolak',
            'kode_induk'  => 'REQ-2026-0145',
            'total'       => 100000000,
            'opsi_mak'    => self::opsiMak(),
            // Baris pembagian awal (persis seperti mockup): kode MAK dan nominal
            'baris'       => [
                ['kode' => '02.01.04.12.001', 'nominal' => 60000000],
                ['kode' => '02.01.04.12.005', 'nominal' => 40000000],
            ],
        ];
    }
}
