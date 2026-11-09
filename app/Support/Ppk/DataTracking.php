<?php

namespace App\Support\Ppk;

use Illuminate\Support\Collection;

/**
 * Sumber data contoh (dummy) untuk menu Tracking role PPK.
 *
 * Id 601-634. Enam baris pertama persis seperti mockup Dashboard Tracking (revisi, diproses, selesai, draf, ditolak, pembayaran).
 * Tahap aktif pada alur 10 langkah mengikuti status; permohonan berstatus "diproses" (id 602) sedang berada di tahap 4, sama seperti mockup.
 * Saat model Eloquent tersedia, ganti isi daftar() dan cari() dengan query pengajuan beserta riwayat tahapnya.
 */
class DataTracking
{
    /** Tahap yang sedang berjalan menurut status (11 = semua tahap selesai) */
    private const TAHAP_AKTIF = ['draft' => 1, 'revisi' => 2, 'ditolak' => 3, 'diproses' => 4, 'pembayaran' => 9, 'selesai' => 11];

    /** Daftar pengajuan untuk tabel Daftar Pengajuan di Dashboard Tracking (34 data) */
    public static function daftar(): Collection
    {
        static $cache = null;

        return $cache ??= collect(self::bangun());
    }

    private static function bangun(): array
    {
        $status = ['revisi', 'diproses', 'selesai', 'draft', 'ditolak', 'pembayaran'];

        return array_map(fn (int $i) => [
            'id'        => 601 + $i,
            'no_surat'  => '021/SPK/PHB-JBT/X/2026',
            'tgl_surat' => '10 Okt 2026',
            'judul'     => 'Pengadaan Komputer Lab Rekayasa Perangkat Lunak',
            'status'    => $status[$i % count($status)],
            'tgl_kirim' => '14 Feb 2026',
        ], range(0, 33));
    }

    /** Satu pengajuan lengkap untuk halaman Detail Tracking; null jika tidak ditemukan */
    public static function cari(int $id): ?array
    {
        $dasar = self::daftar()->firstWhere('id', $id);

        if ($dasar === null) {
            return null;
        }

        $tahapAktif = self::TAHAP_AKTIF[$dasar['status']];

        // Tahap: [judul, pelaku, teks bila selesai, teks bila sedang berjalan]
        $tahap = [
            ['Permohonan Diajukan',                       'Hendra Wijaya / PPK Pengusul',           'Diajukan pada 14 Feb 2026 - 09:30 WIB',   'Sedang diproses.'],
            ['Persetujuan Wadir 2 dan PPK',               'Dr. Ir. H. Hermawan / Wadir 2',          'Disetujui pada 15 Feb 2026 - 11:20 WIB',  'Sedang diproses.'],
            ['Perencanaan/MAK',                           'Dina Rahmawati / Perencanaan',           'Disetujui pada 16 Feb 2026 - 14:00 WIB',  'Sedang diproses.'],
            ['Memo Wadir 2 dan PPK',                      'Dina Rahmawati / PPK',                   'Selesai pada 17 Feb 2026 - 10:15 WIB',    'Sedang diproses. Wadir 2 dan PPK memo sebelum PPBJ.'],
            ['PPBJ',                                      'Pokja / Tim Teknis',                     'Selesai pada 19 Feb 2026 - 13:40 WIB',    'Sedang diproses.'],
            ['Review PP/PPK',                             'Routing dan penugasan PPK (>Rp200 juta)', 'Selesai pada 21 Feb 2026 - 09:10 WIB',   'Sedang diproses.'],
            ['Proses Pengadaan dan Kontrak/SPK',          'Vendor Partner',                         'Selesai pada 25 Feb 2026 - 15:30 WIB',    'Sedang diproses.'],
            ['Pengiriman dan Penerimaan Barang',          'Penerima',                               'Selesai pada 03 Mar 2026 - 11:00 WIB',    'Sedang diproses.'],
            ['Dokumen, Persetujuan, dan Proses Pembayaran', '',                                     'Selesai pada 05 Mar 2026 - 16:20 WIB',    'Sedang diproses.'],
            ['Pembayaran Selesai',                        'Sistem Administrasi PBJ',                'Selesai pada 07 Mar 2026 - 10:00 WIB',    'Sedang diproses.'],
        ];

        $alur = [];
        foreach ($tahap as $i => [$judul, $pelaku, $teksSelesai, $teksAktif]) {
            $nomor = $i + 1;
            $state = $nomor < $tahapAktif ? 'selesai' : ($nomor === $tahapAktif ? 'aktif' : 'menunggu');

            $alur[] = [
                'nomor'  => $nomor,
                'judul'  => $judul,
                'pelaku' => $pelaku,
                'state'  => $state,
                'teks'   => match ($state) {
                    'selesai' => $teksSelesai,
                    'aktif'   => $teksAktif,
                    default   => 'Yang akan datang',
                },
            ];
        }

        return [
            'id'             => $dasar['id'],
            'status'         => $dasar['status'],
            'nomor_surat'    => '920/UN25.3.1/PBJ/2026',
            'unit_pengaju'   => 'Fakultas Teknik - Jurusan Informatika',
            'judul'          => 'Pengadaan Lab Komputer AI Dasar (Kapasitas 30 Unit PC)',
            'tujuan'         => 'Memfasilitasi mata kuliah praktikum Kecerdasan Buatan dan Pembelajaran Mesin yang memiliki peningkatan jumlah mahasiswa di semester ganjil ini. Alat yang ada saat ini spesifikasinya kurang mumpuni untuk komputasi model AI.',
            'total_anggaran' => 'Rp 450.000.000,00',
            'alur'           => $alur,
        ];
    }
}
