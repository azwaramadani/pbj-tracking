<?php

namespace App\Support\Keuangan;

use Illuminate\Support\Collection;

/**
 * Sumber data contoh (dummy) untuk modul Keuangan.
 *
 * Status penerimaan hanya dua: diproses (menunggu realisasi pembayaran) dan selesai. Id 501-534.
 * Lima baris pertama persis seperti mockup Dashboard Keuangan; sisanya dibangkitkan agar paginasi dapat dicoba.
 * Saat model Eloquent tersedia, ganti isi semua() dan cari() dengan query pembayaran beserta relasi vendor, dokumen, dan bukti transfer.
 */
class DataPenerimaan
{
    /** Daftar penerimaan untuk tabel Daftar Penerimaan di Dashboard Keuangan (34 data) */
    public static function semua(): Collection
    {
        static $cache = null;

        return $cache ??= collect(self::bangun());
    }

    private static function bangun(): array
    {
        $spk = ['SPK/PBJ-PLK/2026/02/001', 'SPK/PBJ-PLK/2026/01/018'];

        $baris = [
            ['diproses', $spk[0]],
            ['diproses', $spk[1]],
            ['selesai',  $spk[1]],
            ['selesai',  $spk[0]],
            ['selesai',  $spk[1]],
        ];

        for ($i = 5; $i < 34; $i++) {
            $baris[] = [$i % 3 === 0 ? 'diproses' : 'selesai', $spk[$i % 2]];
        }

        return array_map(fn (array $b, int $i) => [
            'id'            => 501 + $i,
            'no_surat'      => '021/SPK/PHB-JBT/X/2026',
            'tanggal_surat' => '10 Okt 2026',
            'nomor_spk'     => $b[1],
            'judul'         => 'Pengadaan Komputer Lab',
            'status'        => $b[0],
            'tgl_kirim'     => '14 Feb 2026',
        ], $baris, array_keys($baris));
    }

    /**
     * Satu pembayaran lengkap untuk halaman Detail Pembayaran; null jika tidak ditemukan.
     *
     * Tampilan mengikuti status:
     *   diproses : "Detail Pembayaran" (informasi, form realisasi, dokumen pendukung, unggah bukti transfer)
     *   selesai  : "Dokumentasi Penerimaan" (ringkasan pengadaan, bukti realisasi, dokumen & persetujuan terkait)
     */
    public static function cari(int $id): ?array
    {
        $dasar = self::semua()->firstWhere('id', $id);

        if ($dasar === null) {
            return null;
        }

        return [
            'id'     => $dasar['id'],
            'status' => $dasar['status'],

            // ----- Keadaan "diproses" -----
            'nilai_pencairan' => 'Rp 185.000.000',
            'informasi'       => [
                ['label' => 'Nama Vendor / Rekanan', 'nilai' => 'PT. Global Tech Solutions'],
                ['label' => 'Rekening Bank Penerima', 'nilai' => 'Mandiri • 124-000-9921-224'],
                ['label' => 'No. Kontrak Induk', 'nilai' => 'KTR/IT-09/PBJ/2026'],
            ],
            'ringkasan_realisasi'  => ['PAY-2026-0056 | PT. Global Tech Solutions | Rp 185.000.000', 'Nomor Surat: 056/UNIT.TI/PBJ/2026', 'Proses Keuangan: belum dikonfirmasi selesai.'],
            'tanggal_pembayaran'   => '14 Okt 2026, 09:45 WIB',
            'jumlah_dibayar'       => 'Rp 185.000.000',
            'dokumen_pendukung'    => [
                ['nama' => 'BAP_GlobalTech_Final.pdf', 'keterangan' => 'Berita Acara Pembayaran • 2.4 MB'],
                ['nama' => 'Invoice_GTS_56.pdf',       'keterangan' => 'Tagihan Invoice Bermaterai • 1.1 MB'],
            ],
            'pernyataan_dana'      => 'Saya menyatakan bahwa dana transfer senilai Rp 185.000.000 telah didebit dari Kas Negara/Instansi.',

            // ----- Keadaan "selesai" -----
            'waktu_selesai' => '2 November 2026 14:35 WIB',
            'pengadaan'     => [
                'judul'  => 'Pengadaan Lab Komputer AI Dasar (30 Unit PC)',
                'kode'   => 'PBJ-2026-0145 · 920/UN25.3.1/PBJ/2026',
                'status' => 'Selesai',
                'info'   => [
                    ['label' => 'Unit', 'nilai' => 'Fakultas Teknik'],
                    ['label' => 'Nilai Pengadaan', 'nilai' => 'Rp450.000.000'],
                    ['label' => 'Vendor', 'nilai' => 'PT. Global Sarana Abadi'],
                    ['label' => 'PPK', 'nilai' => 'Budi Setiawan'],
                ],
            ],
            'bukti' => [
                'jumlah_dibayar' => 'Rp450.000.000',
                'rincian'        => [
                    ['label' => 'Nomor Referensi', 'nilai' => 'TRF-20261102-0145'],
                    ['label' => 'Metode Pembayaran', 'nilai' => 'Transfer Bank — Mandiri'],
                    ['label' => 'Penerima', 'nilai' => 'PT. Global Sarana Abadi'],
                    ['label' => 'Rekening Tujuan', 'nilai' => 'Mandiri · 124-000-9921-224'],
                    ['label' => 'Tanggal & Waktu', 'nilai' => '2 November 2026 14:35 WIB'],
                    ['label' => 'Dicatat oleh', 'nilai' => 'Linda Lestari — Keuangan'],
                    ['label' => 'Status', 'nilai' => 'Selesai'],
                ],
                'berkas' => ['judul' => 'Bukti Transfer Bank', 'keterangan' => 'Bukti_Transfer_GSA_0145.pdf · PDF · 1,2 MB · 2 Nov 2026'],
            ],
            'terkait' => [
                'rincian' => [
                    ['label' => 'Nomor Perintah', 'nilai' => 'PP/PBJ/2026/0145'],
                    ['label' => 'Diterbitkan oleh', 'nilai' => 'Mahfudh Al Rafif — PPBJ'],
                    ['label' => 'Tanggal Perintah', 'nilai' => '30 Okt 2026 10:00 WIB'],
                    ['label' => 'ID Paket', 'nilai' => 'PAY/PBJ/2026/0145'],
                    ['label' => 'Nomor Kontrak', 'nilai' => 'KTR/PBJ/2026/0145'],
                    ['label' => 'PPK', 'nilai' => 'Budi Setiawan'],
                    ['label' => 'Wadir 2', 'nilai' => 'Dr. Ir. H. Hermawan'],
                ],
                'berkas' => [
                    ['judul' => 'Perintah Pembayaran PPBJ', 'keterangan' => 'Perintah_Pembayaran_0145.pdf · PDF · 1,2 MB · 30 Okt 2026'],
                    ['judul' => 'Paket Pembayaran Disetujui', 'keterangan' => 'Paket_Pembayaran_0145.pdf · PDF · 1,2 MB · 29 Okt 2026'],
                ],
            ],
        ];
    }
}
