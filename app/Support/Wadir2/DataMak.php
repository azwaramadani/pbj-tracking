<?php

namespace App\Support\Wadir2;

use Illuminate\Support\Collection;

/**
 * Sumber data contoh (dummy) untuk fitur "Permohonan MAK" role Wadir 2.
 *
 * Status permohonan MAK hanya ada dua: "tinjau" (perlu aksi Wadir 2) dan "selesai" (memo sudah terbit).
 * Id 301-334. Id 301 dan 302 berstatus tinjau, id 303 berstatus selesai (persis seperti mockup).
 * Saat model Eloquent tersedia, ganti isi daftar() dan cari() dengan query permohonan beserta relasi pengaju, lampiran, memo, dan anggaran.
 */
class DataMak
{
    /** Daftar permohonan MAK untuk tabel Dashboard Permohonan MAK (34 data) */
    public static function daftar(): Collection
    {
        $unit = ['Fakultas Teknik', 'Fakultas Ekonomi', 'Fakultas Hukum', 'Fakultas MIPA', 'Biro Umum'];
        $judul = ['Pengadaan Komputer Lab', 'Pengadaan Proyektor Kelas', 'Pengadaan Meja Kuliah', 'Pengadaan Perangkat Jaringan'];

        return collect(range(0, 33))->map(function (int $i) use ($unit, $judul) {
            $id = 301 + $i;
            // Tiga baris pertama sama seperti mockup; sisanya diberi variasi sederhana
            $mockup = $i < 3;

            return [
                'id'        => $id,
                'no_surat'  => $mockup ? '021/SPK/PHB-JBT/X/2026' : sprintf('%03d/SPK/PHB-JBT/X/2026', 21 + $i),
                'tgl_surat' => '10 Okt 2026',
                'unit'      => $mockup ? 'Fakultas Teknik' : $unit[$i % count($unit)],
                'judul'     => $mockup ? 'Pengadaan Komputer Lab' : $judul[$i % count($judul)],
                'status'    => $i === 2 || ($i > 2 && $i % 3 === 0) ? 'selesai' : 'tinjau',
                'tgl_kirim' => '14 Feb 2026',
            ];
        });
    }

    /** Satu permohonan MAK lengkap untuk halaman Detail Permohonan MAK; null jika tidak ditemukan */
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
                'mata_anggaran'  => '02.01.04.12.001 - Belanja Modal Komputer Lab',
                'jumlah_alokasi' => '120.000.000',
            ],
            // Memo hanya ada setelah Wadir 2 menerbitkannya (status selesai)
            'memo'           => $selesai ? ['nama' => 'Memo_Peretujuan.pdf', 'ukuran' => '1.2 MB'] : null,
            'ppk'            => [
                'judul'            => 'PPK',
                'subjudul'         => 'Unit Perencanaan & IT',
                'status'           => $selesai ? 'disetujui' : 'menunggu',
                'penanggung_jawab' => 'Dr. Ir. H. Hermawan',
                'waktu'            => $selesai ? '15 Februari 2026' : 'Belum disetujui',
            ],
        ];
    }
}
