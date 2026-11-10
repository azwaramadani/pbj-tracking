<?php

namespace App\Support\Penerimaan;

use Illuminate\Support\Collection;

/**
 * Sumber data contoh (dummy) untuk modul Penerimaan.
 *
 * Status hanya dua: "dijalankan" (barang belum dicatat, form dapat diisi) dan "selesai" (penerimaan sudah dicatat, hanya baca).
 * Id 801-834. Lima baris pertama persis seperti mockup Dashboard Penerimaan; sisanya dibangkitkan agar paginasi dapat dicoba.
 * Saat model Eloquent tersedia, ganti isi semua() dan cari() dengan query penerimaan beserta relasi SPK, foto, dan lampiran.
 */
class DataDokumentasi
{
    /** Daftar penerimaan untuk tabel Daftar Penerimaan di Dashboard Penerimaan (34 data) */
    public static function semua(): Collection
    {
        static $cache = null;

        return $cache ??= collect(self::bangun());
    }

    private static function bangun(): array
    {
        $spk = ['SPK/PBJ-PLK/2026/02/001', 'SPK/PBJ-PLK/2026/01/018'];

        $baris = [
            ['dijalankan', $spk[0]],
            ['dijalankan', $spk[1]],
            ['selesai',    $spk[1]],
            ['selesai',    $spk[0]],
            ['selesai',    $spk[1]],
        ];

        for ($i = 5; $i < 34; $i++) {
            $baris[] = [$i % 3 === 0 ? 'dijalankan' : 'selesai', $spk[$i % 2]];
        }

        return array_map(fn (array $b, int $i) => [
            'id'            => 801 + $i,
            'no_surat'      => '021/SPK/PHB-JBT/X/2026',
            'tanggal_surat' => '10 Okt 2026',
            'nomor_spk'     => $b[1],
            'judul'         => 'Pengadaan Komputer Lab',
            'status'        => $b[0],
            'tgl_kirim'     => '14 Feb 2026',
        ], $baris, array_keys($baris));
    }

    /** Satu penerimaan lengkap untuk halaman Dokumentasi Penerimaan; null jika tidak ditemukan */
    public static function cari(int $id): ?array
    {
        $dasar = self::semua()->firstWhere('id', $id);

        if ($dasar === null) {
            return null;
        }

        $selesai = $dasar['status'] === 'selesai';

        return [
            'id'        => $dasar['id'],
            'status'    => $dasar['status'],
            'nomor_spk' => $dasar['nomor_spk'],
            'nomor'     => 'PB-2026-0028',
            'banner'    => 'Penerimaan barang berhasil dicatat. No Penerimaan: PB-2026-0028 · Dicatat 24 Februari 2026, 14:32 WIB',

            // Baris "Informasi Dokumen Kontrak & SPK": [label, isi]
            'informasi' => [
                ['Nomor Surat Permohonan', '015/UNIT.PLK/PBJ/2026'],
                ['Nomor SPK',              $dasar['nomor_spk']],
                ['Vendor / Penyedia',      'PT. Global Sarana Abadi'],
                ['Jenis Pengadaan',        'Barang: Alat Perlengkapan Kantor; Reviewer PP'],
                ['Tanggal SPK',            '10 Februari 2026'],
                ['Metode Pelaksanaan',     'Pengadaan Langsung (PL)'],
                ['Tanggal Pengiriman',     '24 Februari 2026 (Sesuai Jadwal)'],
            ],

            'status_verifikasi' => $selesai ? 'Telah Diterima' : 'Menunggu Penerimaan',

            // Isian "Diterima oleh" yang sudah tersimpan; hanya ada saat status selesai
            'penerima' => $selesai ? [
                'nama'    => 'Hendra Wijaya',
                'unit'    => 'Perlengkapan',
                'tanggal' => '24 Februari 2026',
                'catatan' => 'Barang diterima lengkap dan sesuai SPK.',
            ] : null,

            // TODO Backend: isi "url" dengan alamat foto tersimpan (mis. Storage::url); null = tampil kotak pengganti
            'foto' => [
                ['id' => 1, 'label' => 'Kemasan Box',  'url' => null],
                ['id' => 2, 'label' => 'Nomor Seri',   'url' => null],
                ['id' => 3, 'label' => 'Tampak Depan', 'url' => null],
            ],

            'lampiran' => array_merge([
                ['judul' => 'Surat Jalan (Foto Barang)',        'berkas' => 'IMG_DeliveryOrder_01.jpg'],
                ['judul' => 'Dokumen BAST (Penerimaan Barang)', 'berkas' => 'Draft_BAST_Lengkap.pdf'],
            ], $selesai ? [['judul' => 'Dokumen Serah Terima Penyedia/Vendor', 'berkas' => 'Serah_Terima_Vendor.pdf']] : []),
        ];
    }
}
