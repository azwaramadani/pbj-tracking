<?php

namespace App\Support\Wadir2;

use App\Support\Unit\DataPermohonan;

/**
 * Sumber data contoh (dummy) untuk halaman Detail Permohonan role Wadir 2.
 *
 * Permohonan diambil dari daftar Dashboard (id 101-134). Permohonan id 101 memakai isi persis seperti mockup;
 * permohonan lain diturunkan dari data pengajuan yang sama agar semua tombol "Detail" tetap bisa dibuka.
 * Saat model Eloquent tersedia, ganti isi cari() dengan query permohonan beserta relasi pengaju, lampiran, dan persetujuan.
 */
class DataDetailPermohonan
{
    /** Satu permohonan lengkap untuk halaman Detail Permohonan Wadir 2; null jika tidak ditemukan */
    public static function cari(int $id): ?array
    {
        $dasar = DataPermohonan::dashboard()->firstWhere('id', $id);

        if ($dasar === null) {
            return null;
        }

        // Status kartu persetujuan Perencanaan mengikuti status permohonan
        $statusPerencanaan = in_array($dasar['status'], ['diproses', 'selesai'], true) ? 'disetujui' : 'menunggu';

        $data = [
            'id'              => $dasar['id'],
            'nomor_surat'     => $dasar['no_surat'],
            'unit_pengaju'    => $dasar['unit'],
            'judul'           => $dasar['judul'],
            'tujuan'          => $dasar['deskripsi'],
            'total_anggaran'  => $dasar['nilai_anggaran'],
            'lampiran'        => $dasar['lampiran'],
            'pengaju'         => ['nama' => 'Rian Hidayat, S.T.', 'nip' => '198904122020121002', 'inisial' => 'RH'],
            'catatan_revisi'  => 'Tidak ada.',
            'perencanaan'     => [
                'judul'            => 'Perencanaan',
                'subjudul'         => 'Unit Perencanaan & IT',
                'status'           => $statusPerencanaan,
                'penanggung_jawab' => 'Dr. Ir. H. Hermawan',
                'waktu'            => $statusPerencanaan === 'disetujui' ? '15 Februari 2026' : 'Belum disetujui',
            ],
            // Status persetujuan Wadir 2 (dipakai tampilan PPK pada kartu Keputusan Persetujuan paling bawah)
            'wadir2'          => [
                'judul'            => 'Wadir 2',
                'subjudul'         => 'Unit Perencanaan & IT',
                'status'           => $statusPerencanaan,
                'penanggung_jawab' => 'Dr. Ir. H. Hermawan',
                'waktu'            => $statusPerencanaan === 'disetujui' ? '15 Februari 2026' : 'Belum disetujui',
            ],
        ];

        // Isi persis seperti mockup Detail Permohonan Wadir 2 (Perencanaan sudah menyetujui)
        if ($id === 101) {
            $data['perencanaan']['status'] = 'disetujui';
            $data['perencanaan']['waktu'] = '15 Februari 2026';
            $data['wadir2']['status'] = 'disetujui';
            $data['wadir2']['waktu'] = '15 Februari 2026';
            $data = array_merge($data, [
                'nomor_surat'    => '920/UN25.3.1/PBJ/2026',
                'unit_pengaju'   => 'Fakultas Teknik - Jurusan Informatika',
                'judul'          => 'Pengadaan Lab Komputer AI Dasar (Kapasitas 30 Unit PC)',
                'tujuan'         => 'Memfasilitasi mata kuliah praktikum Kecerdasan Buatan dan Pembelajaran Mesin yang memiliki peningkatan jumlah mahasiswa di semester ganjil ini. Alat yang ada saat ini spesifikasinya kurang mumpuni untuk komputasi model AI.',
                'total_anggaran' => 'Rp 450.000.000,00',
                'lampiran'       => [
                    ['nama' => 'Surat_Permohonan_FT_AI.pdf',                'ukuran' => '1.2 MB'],
                    ['nama' => 'Rincian_Anggaran_Biaya_Komputer.xlsx',      'ukuran' => '485 KB'],
                    ['nama' => 'Spesifikasi_Teknis_Komputer_Lab.pdf',       'ukuran' => '2.4 MB'],
                ],
            ]);
        }

        return $data;
    }
}
