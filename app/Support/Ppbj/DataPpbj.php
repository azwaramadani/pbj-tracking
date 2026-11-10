<?php

namespace App\Support\Ppbj;

use App\Support\Ppk\DataTahap;
use Illuminate\Support\Collection;

/**
 * Sumber data contoh (dummy) untuk role PPBJ: Dashboard (pengadaan), Kontrak & SPK, dan Pembayaran.
 *
 * Id pengadaan 901-934, kontrak 951-984, pembayaran 1001-1034. Lima baris pertama tiap daftar persis seperti mockup;
 * sisanya dibangkitkan agar paginasi dapat dicoba.
 *   Pengadaan  : diproses | revisi | selesai | tinjauan
 *   Kontrak    : tinjauan (perlu unggah) | diproses | selesai
 *   Pembayaran : tinjauan (perlu unggah) | persetujuan | diproses (siap terbit perintah) | pembayaran (menunggu Keuangan) | selesai
 * Saat model Eloquent tersedia, ganti isi method di sini dengan query beserta relasinya.
 */
class DataPpbj
{
    private const SPK = ['SPK/PBJ-PLK/2026/02/001', 'SPK/PBJ-PLK/2026/01/018'];

    /** Daftar pengadaan untuk Dashboard PPBJ (34 data) */
    public static function pengadaan(): Collection
    {
        static $cache = null;

        return $cache ??= collect(range(0, 33))->map(function (int $i) {
            $mockup = [
                ['jasa', 'diproses', 'Andi Wijaya, S.E.'], ['jasa', 'revisi', 'Budi Setiawan, M.T.'],
                ['barang', 'selesai', 'Andi Wijaya, S.E.'], ['jasa', 'tinjauan', 'Budi Setiawan, M.T.'],
                ['barang', 'selesai', 'Andi Wijaya, S.E.'],
            ];
            [$jenis, $status, $pic] = $mockup[$i] ?? [
                $i % 2 ? 'barang' : 'jasa', ['diproses', 'revisi', 'selesai', 'tinjauan'][$i % 4], $i % 3 ? 'Andi Wijaya, S.E.' : 'Budi Setiawan, M.T.',
            ];

            return [
                'id' => 901 + $i, 'no_surat' => '021/SPK/PHB-JBT/X/2026', 'tgl_surat' => '10 Okt 2026',
                'jenis' => $jenis, 'judul' => 'Pengadaan Komputer Lab', 'status' => $status, 'pic' => $pic,
            ];
        });
    }

    /** Daftar kontrak untuk Dashboard Kontrak & SPK (34 data) */
    public static function kontrak(): Collection
    {
        static $cache = null;

        return $cache ??= self::daftarSpk(951, ['tinjauan', 'tinjauan', 'diproses', 'selesai', 'selesai'], ['tinjauan', 'diproses', 'selesai']);
    }

    /** Daftar pembayaran untuk Dashboard Pembayaran (34 data) */
    public static function pembayaran(): Collection
    {
        static $cache = null;

        return $cache ??= self::daftarSpk(1001, ['tinjauan', 'persetujuan', 'diproses', 'pembayaran', 'selesai'], ['tinjauan', 'persetujuan', 'diproses', 'pembayaran', 'selesai']);
    }

    private static function daftarSpk(int $idAwal, array $mockup, array $siklus): Collection
    {
        return collect(range(0, 33))->map(fn (int $i) => [
            'id'        => $idAwal + $i,
            'no_surat'  => '021/SPK/PHB-JBT/X/2026',
            'tgl_surat' => '10 Okt 2026',
            'nomor_spk' => self::SPK[($i === 0 || $i === 3 ? 0 : ($i < 5 ? 1 : $i % 2))],
            'judul'     => 'Pengadaan Komputer Lab',
            'status'    => $mockup[$i] ?? $siklus[$i % count($siklus)],
            'tanggal'   => '14 Feb 2026',
        ]);
    }

    // ----- Detail -----

    /** Pencatatan Metode Pengadaan untuk satu pengadaan; null jika tidak ditemukan */
    public static function detailPengadaan(int $id): ?array
    {
        $dasar = self::pengadaan()->firstWhere('id', $id);

        if ($dasar === null) {
            return null;
        }

        return [
            'id'     => $dasar['id'],
            'status' => $dasar['status'],
            ...self::ringkasanPengadaan(),
            'metode'         => 'Inaproc',
            'opsi_metode'    => ['Pengadaan Langsung', 'Inaproc', 'SPSE', 'Tender', 'Non-Tender'],
            'jenis'          => 'Barang',
            'opsi_jenis'     => ['Barang', 'Jasa'],
            'anggaran_akhir' => 'Rp. 350.000.000,00',
            'vendor'         => 'Omi Pnj',
            'catatan'        => 'Pemesanan 30 unit PC dicatat melalui Inaproc. Hasil negosiasi dan dokumen pemesanan akan diunggah setelah proses eksternal selesai.',
            // Dokumen resmi yang sudah diunggah (ditampilkan hanya-baca pada status tinjauan)
            'dokumen_resmi'  => ['judul' => 'Surat Pesanan', 'keterangan' => 'Surat_Pesanan_AI_0145.pdf · PDF · 1,2 MB · 14 Okt 2026'],
            // Permintaan perbaikan dari PPK; hanya ada pada status revisi
            'revisi'         => $dasar['status'] === 'revisi' ? [
                'diminta_oleh'     => 'Budi Setiawan',
                'peninjau'         => 'PPK — Budi Setiawan',
                'waktu'            => '14 Oktober 2026 09:30 WIB',
                'penanggung_jawab' => 'PPBJ — Mahfudh Al Rafif',
                'catatan'          => 'Lengkapi klausul garansi minimum 3 tahun dan rincian layanan purna jual pada spesifikasi teknis.',
            ] : null,
        ];
    }

    /** Detail Kontrak untuk satu kontrak (bentuk data sama dengan tahap 7 Tracking PPK); null jika tidak ditemukan */
    public static function detailKontrak(int $id): ?array
    {
        $dasar = self::kontrak()->firstWhere('id', $id);

        return $dasar === null ? null : array_merge(DataTahap::kontrak(), [
            'id' => $dasar['id'], 'status' => $dasar['status'], 'pengadaan_id' => 901,
        ]);
    }

    /** Opsi untuk form Unggah / Edit Kontrak & SPK */
    public static function formKontrak(): array
    {
        return [
            'nomor_dokumen'    => 'KTR/PBJ/2026/0145',
            'nomor_pengadaan'  => 'PBJ-2026-0145',
            'opsi_pengadaan'   => ['PBJ-2026-0145', 'PBJ-2026-0146', 'PBJ-2026-0147'],
            'nilai'            => 'Rp450.000.000',
            'status'           => 'Kontrak Terbit',
            'opsi_status'      => ['Kontrak Terbit', 'Menunggu Pengiriman', 'Barang Diterima', 'Selesai'],
            'tanggal_terbit'   => '2026-10-16',
            'tanggal_mulai'    => '2026-10-16',
            'target_pengiriman' => '2026-10-27',
            'tanggal_pengingat' => '2026-10-27',
        ];
    }

    /** Detail pembayaran untuk satu pembayaran; null jika tidak ditemukan */
    public static function detailPembayaran(int $id): ?array
    {
        $dasar = self::pembayaran()->firstWhere('id', $id);

        if ($dasar === null) {
            return null;
        }

        $dokumen = fn (string $judul, string $berkas, string $tanggal) => ['judul' => $judul, 'keterangan' => "{$berkas} · PDF · 1,2 MB · {$tanggal}"];

        return [
            'id'     => $dasar['id'],
            'status' => $dasar['status'],
            ...self::ringkasanPengadaan(),
            'vendor'         => 'PT. Global Sarana Abadi',
            'kontrak_id'     => 953,
            'kontrak'        => 'KTR/PBJ/2026/0145',
            'penerimaan'     => '27 Okt 2026 · 30 PC diterima',
            'catatan_terima' => 'Hendra Wijaya · Perlengkapan · 27 Oktober 2026 15:10 WIB.',
            'catatan_paket'  => 'Paket diteruskan ke PPBJ hanya setelah PPK DAN Wadir 2 menyetujui.',

            // Halaman Unggah Dokumen Pembayaran: hanya dokumen yang baru diunggah (tinjauan) atau seluruh dokumen yang terkirim (persetujuan)
            'dokumen_unggah' => $dasar['status'] === 'tinjauan'
                ? [$dokumen('Spesifikasi Teknis', 'Spesifikasi_AI_v2.pdf', '14 Okt 2026')]
                : [
                    $dokumen('Invoice', 'Invoice_GSA_0145.pdf', '28 Okt 2026'),
                    $dokumen('Kontrak / SPK', 'Kontrak_AI_0145.pdf', '16 Okt 2026'),
                    $dokumen('Kontrak / SPK', 'Kontrak_AI_0145.pdf', '16 Okt 2026'),
                    $dokumen('Spesifikasi Teknis', 'Spesifikasi_AI_v2.pdf', '14 Okt 2026'),
                ],

            // Halaman Persetujuan Paket Pembayaran
            'dokumen_paket'  => [
                $dokumen('Invoice', 'Invoice_GSA_0145.pdf', '28 Okt 2026'),
                $dokumen('Kontrak / SPK', 'Kontrak_AI_0145.pdf', '16 Okt 2026'),
                $dokumen('Kontrak / SPK', 'Kontrak_AI_0145.pdf', '16 Okt 2026'),
                $dokumen('Dokumen Pendukung', 'Surat_Jalan_GSA.pdf', '27 Okt 2026'),
            ],
            'penerbit'       => 'Mahfudh Al Rafif — PPBJ',
            'tujuan'         => 'Keuangan — Linda Lestari',
            'total'          => 'Rp450.000.000',
            'id_paket'       => 'PAY/PBJ/2026/0145',
            'nomor_perintah' => 'PP/PBJ/2026/0145',
            'perintah'       => [
                ['Nomor Perintah',   'PP/PBJ/2026/0145'],
                ['ID Paket',         'PAY/PBJ/2026/0145'],
                ['Nomor Pengadaan',  'PBJ-2026-0145'],
                ['Penerbit',         'Mahfudh Al Rafif — PPBJ'],
                ['Tanggal Terbit',   '30 Oktober 2026 10:00 WIB'],
                ['Tujuan',           'Keuangan — Linda Lestari'],
                ['Jumlah',           'Rp450.000.000'],
                ['Kontrak',          'KTR/PBJ/2026/0145'],
                ['Realisasi',        'Menunggu proses Keuangan'],
            ],
            'berkas_perintah' => $dokumen('Perintah Pembayaran', 'Perintah_Pembayaran_0145.pdf', '30 Okt 2026'),
            // Tombol "Terbitkan Perintah" aktif hanya saat paket sudah disetujui dan perintah belum terbit (status diproses)
            'bisa_terbit'    => $dasar['status'] === 'diproses',
        ];
    }

    /** Ringkasan pengadaan yang sama pada semua halaman detail */
    private static function ringkasanPengadaan(): array
    {
        return [
            'judul' => 'Pengadaan Lab Komputer AI Dasar',
            'kode'  => 'PBJ-2026-0145 · 920/UN25.3.1/PBJ/2026',
            'unit'  => 'Fakultas Teknik',
            'nilai' => 'Rp450.000.000',
            'ppk'   => 'Budi Setiawan',
        ];
    }
}
