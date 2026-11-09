<?php

namespace App\Support\Unit;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Sumber data contoh (dummy) untuk halaman Dokumen.
 *
 * Pengganti sementara sampai model Eloquent `Dokumen` tersedia: cukup ubah isi semua()
 * agar membaca dari database, sedangkan controller dan view tetap sama.
 *
 * Kunci jenis  : kontrak, spk, pembayaran, permohonan
 * Kunci status : aktif, selesai, diproses, menunggu
 */
class DataDokumen
{
    public const LABEL_JENIS = [
        'kontrak'    => 'Kontrak',
        'spk'        => 'SPK',
        'pembayaran' => 'Pembayaran',
        'permohonan' => 'Permohonan',
    ];

    public const LABEL_STATUS = [
        'aktif'    => 'Aktif',
        'selesai'  => 'Selesai',
        'diproses' => 'Diproses',
        'menunggu' => 'Menunggu',
    ];

    /** Seluruh dokumen milik pengguna (24 data) */
    public static function semua(): Collection
    {
        static $cache = null;

        return $cache ??= collect(self::bangun())->map(function (array $d, int $i) {
            return $d + [
                'id'            => $i + 1,
                'label_jenis'   => self::LABEL_JENIS[$d['jenis']],
                'label_status'  => self::LABEL_STATUS[$d['status']],
                'tgl'           => $d['tanggal']->copy()->locale('id')->translatedFormat('d M Y'),
                'bulan'         => $d['tanggal']->format('Y-m'),
            ];
        });
    }

    /** Daftar bulan unik (terbaru dahulu) untuk pilihan filter Tanggal: ['2023-10' => 'Oktober 2023', ...] */
    public static function pilihanBulan(): array
    {
        return self::semua()
            ->sortByDesc('bulan')
            ->mapWithKeys(fn ($d) => [$d['bulan'] => $d['tanggal']->copy()->locale('id')->translatedFormat('F Y')])
            ->all();
    }

    /** Susun 24 dokumen: 6 pertama persis seperti mockup, sisanya dibangkitkan */
    private static function bangun(): array
    {
        $d = fn (int $bulan, int $hari) => Carbon::create(2023, $bulan, $hari);

        $data = [
            ['jenis' => 'kontrak',    'status' => 'aktif',    'judul' => 'Kontrak Sewa Kendaraan Dinas Operasional',   'ukuran' => '2.4 MB', 'ikon' => 'dokumen', 'tanggal' => $d(10, 12)],
            ['jenis' => 'spk',        'status' => 'selesai',  'judul' => 'SPK Renovasi Toilet Lantai 2 Gedung A',      'ukuran' => '2.4 MB', 'ikon' => 'gear',    'tanggal' => $d(10, 10)],
            ['jenis' => 'pembayaran', 'status' => 'diproses', 'judul' => 'Formulir Pembayaran Vendor IT Infrastructure', 'ukuran' => '2.4 MB', 'ikon' => 'dokumen', 'tanggal' => $d(10, 8)],
            ['jenis' => 'permohonan', 'status' => 'menunggu', 'judul' => 'Permohonan Penambahan Bandwidth Internet',   'ukuran' => '2.4 MB', 'ikon' => 'dokumen', 'tanggal' => $d(10, 15)],
            ['jenis' => 'kontrak',    'status' => 'aktif',    'judul' => 'Kontrak Maintenance Elevator Unit Utama',    'ukuran' => '2.4 MB', 'ikon' => 'gear',    'tanggal' => $d(10, 1)],
            ['jenis' => 'spk',        'status' => 'menunggu', 'judul' => 'SPK Penyediaan Konsumsi Harian Karyawan',    'ukuran' => '2.4 MB', 'ikon' => 'dokumen', 'tanggal' => $d(10, 14)],
        ];

        $judul = [
            'kontrak'    => ['Kontrak Pengadaan Perangkat Laboratorium', 'Kontrak Jasa Kebersihan Gedung Utama', 'Kontrak Langganan Internet Kampus', 'Kontrak Perawatan AC Gedung Rektorat'],
            'spk'        => ['SPK Perbaikan Atap Gedung B', 'SPK Pengadaan Meja Kerja Staf', 'SPK Pengecatan Ulang Ruang Kelas', 'SPK Instalasi Jaringan Lantai 3'],
            'pembayaran' => ['Formulir Pembayaran Vendor Katering', 'Bukti Pembayaran Sewa Proyektor', 'Formulir Pembayaran Jasa Konsultan', 'Rekap Pembayaran Termin Pertama'],
            'permohonan' => ['Permohonan Pengadaan Printer Unit', 'Permohonan Perbaikan Lift Gedung A', 'Permohonan Penambahan Lisensi Software', 'Permohonan Pengadaan Alat Laboratorium'],
        ];
        $jenis  = array_keys($judul);
        $status = array_keys(self::LABEL_STATUS);
        $ukuran = ['1.8 MB', '3.1 MB', '0.9 MB', '2.7 MB'];

        for ($i = 0; $i < 18; $i++) {
            $j = $jenis[$i % 4];
            $data[] = [
                'jenis'   => $j,
                'status'  => $status[($i + intdiv($i, 4)) % 4],
                'judul'   => $judul[$j][intdiv($i, 4) % 4],
                'ukuran'  => $ukuran[$i % 4],
                'ikon'    => $i % 3 === 1 ? 'gear' : 'dokumen',
                'tanggal' => Carbon::create(2023, 9, 30)->subDays($i * 2),
            ];
        }

        return $data;
    }
}
