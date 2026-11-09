<?php

namespace App\Support\Wadir2;

use Illuminate\Support\Collection;

/**
 * Sumber data contoh (dummy) untuk halaman Pembayaran role Wadir 2.
 *
 * Saat model Eloquent tersedia, ganti isi semua() dengan query paket pembayaran yang ditugaskan kepada Wadir 2;
 * bentuk array (kunci) dipertahankan agar controller dan view tidak perlu diubah.
 *
 * Kunci jenis  : jasa, barang
 * Kunci status : diproses, selesai, tinjauan
 */
class DataPembayaran
{
    /** Seluruh permohonan yang ditugaskan kepada Wadir 2 (34 data, id 201-234) */
    public static function semua(): Collection
    {
        static $cache = null;

        return $cache ??= collect(self::bangun());
    }

    private static function bangun(): array
    {
        // Lima baris pertama persis seperti mockup; sisanya dibangkitkan agar paginasi dapat dicoba
        $baris = [
            ['jasa',   'diproses', 120000000],
            ['jasa',   'diproses', 320000000],
            ['barang', 'selesai',  400000000],
            ['jasa',   'tinjauan', 90000000],
            ['barang', 'tinjauan', 120000000],
        ];

        $jenis = ['jasa', 'barang'];
        $status = ['diproses', 'selesai', 'tinjauan'];
        for ($i = 5; $i < 34; $i++) {
            $baris[] = [$jenis[$i % 2], $status[$i % 3], 50000000 + ($i * 13000000) % 450000000];
        }

        return array_map(fn (array $b, int $i) => [
            'id'            => 201 + $i,
            'no_surat'      => '021/SPK/PHB-JBT/X/2026',
            'tanggal_surat' => '10 Okt 2026',
            'jenis'         => $b[0],
            'judul'         => 'Pengadaan Komputer Lab',
            'nilai'         => 'Rp ' . number_format($b[2], 0, ',', '.'),
            'status'        => $b[1],
        ], $baris, array_keys($baris));
    }

    /**
     * Satu paket pembayaran lengkap untuk halaman "Persetujuan Paket Pembayaran"; null jika tidak ditemukan.
     *
     * Dua varian tampilan sesuai mockup (halaman 4 dan 5):
     *   - paket berstatus "selesai"   : Perencanaan sudah Disetujui, tombol "Tentukan Keputusan" tidak ditampilkan
     *   - paket lain (diproses/tinjauan): Perencanaan masih Menunggu, tombol "Tentukan Keputusan" ditampilkan
     * Saat model Eloquent tersedia, ganti isi method ini dengan query paket + relasi dokumen dan persetujuan.
     */
    public static function cari(int $id): ?array
    {
        $dasar = self::semua()->firstWhere('id', $id);

        if ($dasar === null) {
            return null;
        }

        $perencanaanSetuju = $dasar['status'] === 'selesai';
        $nomor = sprintf('%04d', 145 + ($id - 201));

        return [
            'id'          => $id,
            'judul'       => 'Pengadaan Lab Komputer AI Dasar',
            'kode'        => "PBJ-2026-{$nomor} · 920/UN25.3.1/PBJ/2026",
            'unit'        => 'Fakultas Teknik',
            // Contoh 201 dan 203 persis seperti mockup; paket lain memakai nilai dari daftar
            'nilai'       => in_array($id, [201, 203], true) ? 'Rp450.000.000' : str_replace('Rp ', 'Rp', $dasar['nilai']),
            'vendor'      => 'PT. Global Sarana Abadi',
            'ppk'         => 'Budi Setiawan',
            'catatan'     => 'Paket diteruskan ke PPBJ hanya setelah PPK DAN Wadir 2 menyetujui.',

            // Baris "Informasi Paket": [label, isi]
            'informasi'   => [
                ['ID Paket',          "PAY/PBJ/2026/{$nomor}"],
                ['Kontrak',           "KTR/PBJ/2026/{$nomor}"],
                ['Persetujuan PPK',   '29 Okt 2026 09:15 WIB'],
                ['Catatan PPK',       'Nilai sesuai kontrak.'],
                ['Waktu Review',      '29 Okt 2026 13:30 WIB'],
                ['Status Anda',       'Menunggu keputusan'],
            ],

            // Kartu status persetujuan (urutan tetap: Wadir 2 lalu Perencanaan)
            'wadir2'      => [
                'judul'            => 'Wadir 2',
                'subjudul'         => 'Wakil Direktur 2',
                'status'           => 'disetujui',
                'penanggung_jawab' => 'Dr. Ir. H. Hermawan',
                'waktu'            => '15 Februari 2026',
            ],
            'perencanaan' => [
                'judul'            => 'Perencanaan',
                'subjudul'         => 'Unit Perencanaan & IT',
                'status'           => $perencanaanSetuju ? 'disetujui' : 'menunggu',
                'penanggung_jawab' => $perencanaanSetuju ? 'Dr. Ir. H. Hermawan' : 'Dina Rahmawati',
                'waktu'            => $perencanaanSetuju ? '15 Februari 2026' : 'Belum disetujui',
            ],

            // Tombol "Tentukan Keputusan" hanya muncul selama persetujuan belum lengkap
            'bisa_memutuskan' => ! $perencanaanSetuju,

            'dokumen'     => [
                ['judul' => 'Invoice',            'berkas' => 'Invoice_GSA_0145.pdf',  'tipe' => 'PDF', 'ukuran' => '1,2 MB', 'tanggal' => '28 Okt 2026'],
                ['judul' => 'Kontrak / SPK',      'berkas' => 'Kontrak_AI_0145.pdf',   'tipe' => 'PDF', 'ukuran' => '1,2 MB', 'tanggal' => '16 Okt 2026'],
                ['judul' => 'Kontrak / SPK',      'berkas' => 'Kontrak_AI_0145.pdf',   'tipe' => 'PDF', 'ukuran' => '1,2 MB', 'tanggal' => '16 Okt 2026'],
                ['judul' => 'Dokumen Pendukung',  'berkas' => 'Surat_Jalan_GSA.pdf',   'tipe' => 'PDF', 'ukuran' => '1,2 MB', 'tanggal' => '27 Okt 2026'],
            ],
        ];
    }
}
