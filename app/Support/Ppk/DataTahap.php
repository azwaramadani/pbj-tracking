<?php

namespace App\Support\Ppk;

use App\Support\Pp\DataPengadaan;
use App\Support\Wadir2\DataMak;

/**
 * Sumber data contoh (dummy) untuk halaman detail tiap tahap pada "Alur Perjalanan Pengadaan" (Tracking PPK).
 *
 * Tahap 1-3 : Detail Permohonan (komponen x-unit.ringkasan-permohonan)
 * Tahap 4-5 : Detail Permohonan dengan Identitas Pengaju + Form Alokasi Anggaran (tahap 5 menambah Keputusan Persetujuan)
 * Tahap 6   : Detail Pengadaan (Review PP/PPK)
 * Tahap 7   : Detail Kontrak / SPK
 * Tahap 8   : Dokumentasi Penerimaan (barang)
 * Tahap 9-10: memakai ulang halaman Persetujuan Paket Pembayaran (Wadir 2) dan Dokumentasi Penerimaan selesai (Keuangan)
 *
 * Saat model Eloquent tersedia, ganti isi method-method di bawah dengan query tahap yang bersangkutan.
 */
class DataTahap
{
    /** Tahap 1-3: Informasi Surat Permohonan, Lampiran Dokumen, dan kartu status persetujuan */
    public static function permohonan(int $nomor, string $state): array
    {
        $wadir2 = [
            'judul' => 'Wadir 2', 'subjudul' => 'Wakil Direktur 2', 'status' => 'disetujui',
            'penanggung_jawab' => 'Dr. Ir. H. Hermawan', 'waktu' => '15 Februari 2026',
        ];

        // Tahap 3 (Perencanaan/MAK): kartu Perencanaan tampil lebih dulu; menunggu selama tahap berjalan, disetujui bila tahap sudah lewat
        $kartu = $nomor === 3
            ? [[
                'judul' => 'Perencanaan', 'subjudul' => 'Unit Perencanaan & IT',
                'status' => $state === 'selesai' ? 'disetujui' : 'menunggu',
                'penanggung_jawab' => 'Dina Rahmawati',
                'waktu' => $state === 'selesai' ? '16 Februari 2026' : 'Belum disetujui',
            ], $wadir2]
            : [$wadir2, [
                'judul' => 'PPK', 'subjudul' => 'PPK', 'status' => 'disetujui',
                'penanggung_jawab' => 'Dr. Ir. H. Hermawan', 'waktu' => '15 Februari 2026',
            ]];

        $judul = 'Pengadaan Komputer Lab Rekayasa Perangkat Lunak';

        return [
            'baris' => [
                ['Nomor Surat',         '021/UN1.FT/PBJ/2026'],
                ['Judul Pengadaan',     $judul],
                ['Judul Pengadaan',     $judul],
                ['Asal Unit Pengusul',  'Fakultas Teknik - Perencanaan & IT'],
                ['Deskripsi & Urgensi', 'Sebab spesifikasi komputer di Lab RPL saat ini masih menggunakan Core i3 generasi ke-4, tidak mumpuni lagi untuk menjalankan engine pengembangan modern serta IDE berat seperti Android Studio dan Visual Studio.'],
                ['Tanggal Pengajuan',   '14 Februari 2026 - 09:30 WIB'],
            ],
            'lampiran' => [
                ['nama' => 'RAB_Pengadaan_Lab_RPL.xlsx',          'ukuran' => '340 KB'],
                ['nama' => 'KAK_Spesifikasi_Teknis_Komputer.pdf', 'ukuran' => '1.2 MB'],
            ],
            'kartu' => $kartu,
        ];
    }

    /** Tahap 4-5: informasi pengadaan, lampiran, identitas pengaju, dan form alokasi anggaran (data sama seperti Permohonan MAK) */
    public static function memo(): array
    {
        return [
            'permohonan' => DataMak::cari(301),
            'memo'       => [
                ['label' => 'Memo Persetujuan Wadir 2', 'nama' => 'memo_wadir2', 'isi' => 'Permohonan sudah sesuai.'],
                ['label' => 'Memo Persetujuan PPK',     'nama' => 'memo_ppk',    'isi' => 'Permohonan sudah sesuai.'],
            ],
        ];
    }

    /** Tahap 6: Detail Pengadaan (Review PP/PPK) */
    public static function review(): array
    {
        return [
            'pengadaan' => DataPengadaan::cari(401),
            'tanda_tangan' => [
                'berkas'     => ['nama' => 'Spesifikasi_Teknis_Server_Pusdatin_v2.pdf', 'ukuran' => '2.4 MB'],
                'keterangan' => 'Belum ada dokumen hasil. Kontrak belum diterbitkan pada tahap ini.',
            ],
        ];
    }

    /** Tahap 7: Detail Kontrak / SPK */
    public static function kontrak(): array
    {
        return [
            'judul'   => 'Pengadaan Lab Komputer AI Dasar',
            'kode'    => 'PBJ-2026-0145 · 920/UN25.3.1/PBJ/2026',
            'unit'    => 'Fakultas Teknik',
            'nilai'   => 'Rp450.000.000',
            'ppk'     => 'Budi Setiawan',
            // Id pengadaan untuk tombol "Lihat Pengadaan" (TODO Backend: id pengadaan milik kontrak ini)
            'pengadaan_id' => 401,
            'informasi' => [
                ['Nomor Dokumen',      'KTR/PBJ/2026/0145'],
                ['Jenis Dokumen',      'Surat Kontrak'],
                ['Nomor Pengadaan',    'PBJ-2026-0145'],
                ['Vendor',             'PT. Global Sarana Abadi'],
                ['Nilai',              'Rp450.000.000'],
                ['Tanggal Terbit',     '16 Oktober 2026'],
                ['Tanggal Mulai',      '16 Oktober 2026'],
                ['Target Pengiriman',  '27 Oktober 2026'],
                ['Termin Pembayaran',  '1 termin · 100% setelah BAST / BAP disahkan'],
                ['Status',             'Menunggu Pengiriman'],
            ],
            'pengingat' => [
                ['Target',           '27 Oktober 2026'],
                ['Jadwal Notifikasi', 'H-7, H-3, H-1'],
                ['Penerima',         'PPBJ & Perlengkapan'],
            ],
            'status_barang' => 'Barang belum diterima.',
            'dokumen' => [
                ['judul' => 'Surat Kontrak',  'keterangan' => 'Kontrak_AI_0145.pdf · PDF · 1,2 MB · 16 Okt 2026'],
                ['judul' => 'SPK',            'keterangan' => 'SPK_AI_0145.pdf · PDF · 1,2 MB · 16 Okt 2026'],
                ['judul' => 'Surat Pesanan',  'keterangan' => 'PO_AI_0145.pdf · PDF · 1,2 MB · 16 Okt 2026'],
                ['judul' => 'Dokumen Vendor', 'keterangan' => 'NIB_NPWP_GSA.pdf · PDF · 1,2 MB · 15 Okt 2026'],
            ],
        ];
    }

    /** Tahap 8: Dokumentasi Penerimaan barang */
    public static function penerimaan(): array
    {
        return [
            'banner'  => 'Penerimaan barang berhasil dicatat. No Penerimaan: PB-2026-0028 · Dicatat 24 Februari 2026, 14:32 WIB',
            'nomor'   => 'PB-2026-0028',
            'informasi' => [
                ['Nomor Surat Permohonan', '015/UNIT.PLK/PBJ/2026'],
                ['Nomor SPK',              'SPK/PBJ-PLK/2026/02/001'],
                ['Vendor / Penyedia',      'PT. Global Sarana Abadi'],
                ['Jenis Pengadaan',        'Barang: Alat Perlengkapan Kantor; Reviewer PP'],
                ['Tanggal SPK',            '10 Februari 2026'],
                ['Metode Pelaksanaan',     'Pengadaan Langsung (PL)'],
                ['Tanggal Pengiriman',     '24 Februari 2026 (Sesuai Jadwal)'],
            ],
            'status_verifikasi' => 'Menunggu Penerimaan',
            // TODO Backend: isi "url" dengan alamat foto tersimpan (mis. Storage::url); null = tampil kotak pengganti
            'foto' => [
                ['label' => 'Kemasan Box',  'url' => null],
                ['label' => 'Nomor Seri',   'url' => null],
                ['label' => 'Tampak Depan', 'url' => null],
            ],
            'lampiran' => [
                ['judul' => 'Surat Jalan (Foto Barang)',       'berkas' => 'IMG_DeliveryOrder_01.jpg'],
                ['judul' => 'Dokumen BAST (Penerimaan Barang)', 'berkas' => 'Draft_BAST_Lengkap.pdf'],
            ],
        ];
    }
}
