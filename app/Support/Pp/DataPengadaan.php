<?php

namespace App\Support\Pp;

use Illuminate\Support\Collection;

/**
 * Sumber data contoh (dummy) untuk modul PP (Pejabat Pengadaan).
 *
 * Status pengadaan: diproses (perlu aksi PP), selesai, tinjauan, ditolak. Id 401-434.
 * Lima baris pertama persis seperti mockup Dashboard PP; sisanya dibangkitkan agar paginasi dapat dicoba.
 * Saat model Eloquent tersedia, ganti isi semua() dan cari() dengan query pengadaan beserta relasi vendor, lampiran, PPK, dan catatan review.
 */
class DataPengadaan
{
    /** Daftar pengadaan untuk tabel Daftar Permohonan di Dashboard PP (34 data) */
    public static function semua(): Collection
    {
        static $cache = null;

        return $cache ??= collect(self::bangun());
    }

    private static function bangun(): array
    {
        $baris = [
            ['jasa',   'diproses', 120000000],
            ['jasa',   'diproses', 320000000],
            ['barang', 'selesai',  400000000],
            ['jasa',   'tinjauan', 90000000],
            ['barang', 'ditolak',  120000000],
        ];

        $jenis = ['jasa', 'barang'];
        $status = ['diproses', 'selesai', 'tinjauan', 'ditolak'];
        for ($i = 5; $i < 34; $i++) {
            $baris[] = [$jenis[$i % 2], $status[$i % 4], 50000000 + ($i * 13000000) % 450000000];
        }

        return array_map(fn (array $b, int $i) => [
            'id'            => 401 + $i,
            'no_surat'      => '021/SPK/PHB-JBT/X/2026',
            'tanggal_surat' => '10 Okt 2026',
            'jenis'         => $b[0],
            'judul'         => 'Pengadaan Komputer Lab',
            'nilai'         => 'Rp ' . number_format($b[2], 0, ',', '.'),
            'status'        => $b[1],
        ], $baris, array_keys($baris));
    }

    /**
     * Satu pengadaan lengkap untuk halaman Detail Pengadaan; null jika tidak ditemukan.
     *
     * Tampilan mengikuti status:
     *   diproses / tinjauan : lencana "Perlu Perbaikan" + tombol "Tambahkan catatan" dan "Konfirmasi Review"
     *   ditolak  : lencana "DITOLAK" + kartu Identitas PPK, catatan hanya-baca
     *   selesai  : tanpa lencana dan tanpa tombol, catatan hanya-baca
     */
    public static function cari(int $id): ?array
    {
        $dasar = self::semua()->firstWhere('id', $id);

        if ($dasar === null) {
            return null;
        }

        return [
            'id'             => $dasar['id'],
            'status'         => $dasar['status'],
            'vendor'         => 'PT. Indo Cloud Nusantara',
            'nilai_hps'      => 'Rp 185.000.000',
            'jenis'          => 'Pengadaan Server Utama Pusdatin',
            'kode_mak'       => '023.04.12.6582.001.A',
            'catatan'        => 'Revisi dokumen diperlukan sebelum tanda tangan akhir. Mohon perbarui lampiran sesuai catatan di atas, lalu unggah kembali melalui layar pengajuan ulang PPBJ.',
            'versi_catatan'  => '1.0',
            'lampiran'       => [
                ['nama' => 'Spesifikasi_Teknis_Server_Pusdatin_v2.pdf', 'ukuran' => '2.4 MB'],
                ['nama' => 'Rencana_Anggaran_Biaya_HPS_Final.xlsx',     'ukuran' => '1.1 MB'],
                ['nama' => 'Draft_Kontrak_Perjanjian_Kerjasama.docx',   'ukuran' => '3.8 MB'],
            ],
            'ppk'            => ['nama' => 'Rian Hidayat, S.T.', 'nip' => '198904122020121002', 'inisial' => 'RH'],
            // Identitas PP yang meneruskan catatan (dipakai tampilan PPK pada kartu "Identitas PP")
            'pp'             => ['nama' => 'Rian Hidayat, S.T.', 'nip' => '198904122020121002', 'inisial' => 'RH'],
            // Bahan teks pada modal Konfirmasi Review
            'tanggal_review' => '27 Oktober 2026',
            'nama_reviewer'  => 'Hendra Wijaya',
            'judul_review'   => 'Pengadaan Lab Komputer AI Dasar',
            'nomor_surat'    => '920/UN25.3.1/PBJ/2026',
        ];
    }
}
