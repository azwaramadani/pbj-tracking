<?php

namespace App\Support\Unit;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Sumber data contoh (dummy) untuk halaman role User Unit: Dashboard, Daftar Permohonan, Detail Permohonan, dan Tracking.
 *
 * Kelas ini hanya pengganti sementara. Saat model Eloquent `Permohonan` tersedia,
 * cukup ganti isi method semua(), dashboard(), dan cari() agar membaca dari database;
 * controller dan view tidak perlu diubah karena bentuk datanya (array dengan kunci yang sama) tetap.
 *
 * Kunci status internal: draft, menunggu, diproses, selesai.
 */
class DataPermohonan
{
    /** Label status yang ditampilkan di halaman Daftar Permohonan */
    public const LABEL_STATUS = [
        'draft'    => 'Draft',
        'menunggu' => 'Waiting Approval',
        'diproses' => 'Processing',
        'selesai'  => 'Completed',
    ];

    /** Label tahap yang ditampilkan pada lencana di halaman Detail Permohonan */
    private const LABEL_TAHAP = [
        'draft'    => 'Draft',
        'menunggu' => 'Menunggu Approval',
        'diproses' => 'Sedang Ditinjau Perencanaan',
        'selesai'  => 'Pengadaan Selesai',
    ];

    /** Seluruh permohonan milik pengguna (45 data, id 1-45) */
    public static function semua(): Collection
    {
        static $cache = null;

        return $cache ??= collect(self::bangunDaftar())
            ->map(fn (array $baris) => self::lengkapi($baris));
    }

    /** Lima pengajuan terbaru untuk tabel di Dashboard (id 101-105) */
    public static function dashboard(): Collection
    {
        $unit = 'Fakultas Teknik - Perencanaan & IT';

        $baris = [
            [101, '021/UN1.FT/PBJ/2026', 'Pengadaan Komputer Lab Rekayasa Perangkat Lunak', 'diproses', Carbon::create(2026, 2, 14, 9, 30)],
            [102, '019/UN1.FT/PBJ/2026', 'Renovasi Ruang Kuliah 302 Gedung Dekanat',        'selesai',  Carbon::create(2026, 2, 11, 9, 0)],
            [103, '018/UN1.FT/PBJ/2026', 'Pembelian Alat Peraga Elektronika Lanjut',        'menunggu', Carbon::create(2026, 2, 9, 9, 0)],
            [104, '015/UN1.FT/PBJ/2026', 'Sewa Cloud Server untuk Repository Riset',        'diproses', Carbon::create(2026, 2, 5, 9, 0)],
            [105, '012/UN1.FT/PBJ/2026', 'Pengadaan AC Split Ruang Seminar FT',             'selesai',  Carbon::create(2026, 1, 29, 9, 0)],
        ];

        return collect($baris)->map(function (array $b) use ($unit) {
            [$id, $noSurat, $judul, $status, $kirim] = $b;

            $data = self::lengkapi([
                'id'      => $id,
                'no_surat' => $noSurat,
                'judul'   => $judul,
                'unit'    => $unit,
                'status'  => $status,
                'kirim'   => $kirim,
                'update'  => $kirim->copy()->addDays(2),
            ]);

            // Data lengkap untuk permohonan contoh pada mockup halaman Detail Permohonan
            if ($id === 101) {
                $data['deskripsi'] = 'Sebab spesifikasi komputer di Lab RPL saat ini masih menggunakan Core i3 generasi ke-4, tidak mumpuni lagi untuk menjalankan engine pengembangan modern serta IDE berat seperti Android Studio dan Visual Studio.';
                $data['lampiran'] = [
                    ['nama' => 'RAB_Pengadaan_Lab_RPL.xlsx',            'ukuran' => '340 KB'],
                    ['nama' => 'KAK_Spesifikasi_Teknis_Komputer.pdf',   'ukuran' => '1.2 MB'],
                    ['nama' => 'Surat_Permohonan_Ttd_Dekan.pdf',        'ukuran' => '450 KB'],
                ];
                $data['log'] = [
                    ['judul' => 'Pengajuan Selesai', 'keterangan' => '14 Feb 2026 - PPK Unit',          'state' => 'selesai'],
                    ['judul' => 'Disetujui Wadir 2', 'keterangan' => '15 Feb 2026 - Wadir Bidang Umum', 'state' => 'selesai'],
                    ['judul' => 'Review Perencanaan', 'keterangan' => 'Sedang Berlangsung',             'state' => 'aktif'],
                ];
            }

            return $data;
        });
    }

    /** Cari satu permohonan (dari daftar maupun dashboard) berdasarkan id; null jika tidak ada */
    public static function cari(int $id): ?array
    {
        return self::semua()->merge(self::dashboard())->firstWhere('id', $id);
    }

    /**
     * Data halaman Tracking Permohonan: 10 tahap alur pengadaan, ringkasan berkas,
     * contoh status khusus, dan contoh permohonan yang dipecah. Null jika permohonan tidak ada.
     *
     * Tahap aktif ditentukan dari status permohonan:
     *   draft = belum ada tahap berjalan, menunggu = tahap 2, diproses = tahap 4, selesai = semua tahap selesai.
     */
    public static function tracking(int $id): ?array
    {
        $permohonan = self::cari($id);

        if ($permohonan === null) {
            return null;
        }

        /** @var Carbon|null $kirim */
        $kirim = $permohonan['kirim'];

        $aktifKe = ['draft' => 0, 'menunggu' => 2, 'diproses' => 4, 'selesai' => 11][$permohonan['status']];

        // [judul, penanggung jawab, jumlah hari estimasi dari tanggal kirim, keterangan tetap bila tahap belum selesai]
        $tahap = [
            ['Pengajuan Permohonan',                   'PPK Pengusul',                      0,  null],
            ['Approval Wakil Direktur 2',              'Wadir Bidang Umum & Keuangan',      1,  null],
            ['Peninjauan Tim Perencanaan',             'Bagian Perencanaan',                2,  null],
            ['Penentuan MAK (Mata Anggaran)',          'Tim Keuangan PBJ',                  4,  'Status: Sedang dicocokkan dengan ketersediaan pagu dana anggaran'],
            ['PPBJ (Pejabat Pengadaan)',               'Pokja / Tim Teknis',                5,  null],
            ['PP / PPK Review',                        'Review Kelengkapan KAK',            8,  null],
            ['Proses Procurement (Lelang/Penunjukan)', 'Vendor Partner',                    12, null],
            ['Penerimaan Barang (Goods Received)',     'Panitia Penerima Hasil Pekerjaan',  19, null],
            ['Verifikasi Pembayaran (Payment)',        'Keuangan & Bendahara',              24, null],
            ['Pengadaan Selesai',                      'Sistem Administrasi PBJ',           29, 'Pencatatan inventarisasi aset resmi'],
        ];
        // Jam persetujuan contoh untuk tahap yang sudah selesai
        $jam = [1 => [11, 20], 2 => [14, 0]];

        $alur = [];
        foreach ($tahap as $i => [$judul, $penanggungJawab, $hari, $keterangan]) {
            $nomor = $i + 1;
            $state = $nomor < $aktifKe ? 'selesai' : ($nomor === $aktifKe ? 'aktif' : 'menunggu');
            $tanggal = $kirim?->copy()->addDays($hari);

            if ($tanggal && isset($jam[$i])) {
                $tanggal->setTime(...$jam[$i]);
            }

            if ($state === 'selesai' && $nomor < 10) {
                $awal = $nomor === 1 ? 'Diajukan pada ' : 'Disetujui pada ';
                $teks = $awal . self::tgl($tanggal, 'd M Y') . ' - ' . $tanggal->format('H:i') . ' WIB';
            } elseif ($keterangan !== null && ($state === 'aktif' || $nomor === 10)) {
                $teks = $keterangan;
            } elseif ($state === 'aktif') {
                $teks = 'Status: Sedang diproses';
            } else {
                $teks = 'Estimasi proses: ' . ($tanggal ? self::tgl($tanggal, 'd M Y') : '-');
            }

            $alur[] = [
                'nomor'   => $nomor,
                'judul'   => $judul,
                'peran'   => $penanggungJawab,
                'teks'    => $teks,
                'state'   => $state,
            ];
        }

        return [
            'permohonan' => $permohonan,
            'alur'       => $alur,
            'ringkasan'  => [
                'pagu'      => 'Rp ' . number_format($id === 101 ? 340000000 : 50000000 + $id * 7500000, 0, ',', '.') . ',-',
                'pejabat'   => 'Hendra Wijaya, M.T.',
                'target'    => $kirim ? self::tgl($kirim->copy()->addDays(29), 'd F Y') . ' (Paling Lambat)' : '-',
            ],
            // Contoh status khusus: [label, warna, jumlah, judul, keterangan]
            'kasus' => [
                ['label' => 'Ditolak',      'warna' => 'tolak',     'jumlah' => 1, 'judul' => 'Alasan penolakan',    'keterangan' => 'Permohonan tidak sesuai dengan prioritas anggaran tahun berjalan.'],
                ['label' => 'Ditangguhkan', 'warna' => 'tangguh',   'jumlah' => 1, 'judul' => 'Alasan penangguhan',  'keterangan' => 'Pagu anggaran belum final, menunggu konfirmasi revisi alokasi.'],
                ['label' => 'Perlu Revisi', 'warna' => 'revisi',    'jumlah' => 1, 'judul' => 'Catatan reviewer',    'keterangan' => 'Perlu klarifikasi spesifikasi teknis dan justifikasi pemilihan metode pengadaan.'],
                ['label' => 'Terlambat',    'warna' => 'terlambat', 'jumlah' => 1, 'judul' => 'Keterlambatan',       'keterangan' => 'Target penyelesaian mundur 7 hari karena keterlambatan verifikasi dokumen pendukung.'],
            ],
            // Contoh permohonan yang dipecah menjadi child procurement
            'split' => [
                'jumlah'  => 2,
                'judul'   => 'Contoh split permohonan',
                'daftar'  => [
                    ['kode' => 'REQ-2026-0145',   'keterangan' => 'Parent request'],
                    ['kode' => 'REQ-2026-0145-A', 'keterangan' => 'MAK Operasional'],
                    ['kode' => 'REQ-2026-0145-B', 'keterangan' => 'MAK Pemeliharaan'],
                ],
                'catatan' => 'Dokumen pengadaan child tetap terlacak ke permohonan induk dan dapat diikuti secara terpisah tanpa kehilangan konteks asal.',
            ],
        ];
    }

    /** Susun 45 baris dasar: 8 baris pertama persis seperti mockup, sisanya dibangkitkan */
    private static function bangunDaftar(): array
    {
        $unit = 'Fakultas Teknik - Perencanaan & IT';
        $d = fn (int $bulan, int $hari) => Carbon::create(2023, $bulan, $hari, 9, 0);

        $baris = [
            [1, 'SP-2023/10/082', 'Pengadaan Alat Tulis Kantor Semester II',  'diproses', $d(10, 12), $d(10, 14)],
            [2, 'SP-2023/10/083', 'Renovasi Ruang Meeting Utama Gedung A',    'menunggu', $d(10, 14), $d(10, 14)],
            [3, 'SP-2023/10/084', 'Sewa Laptop Operasional Q4 Staf Lapangan', 'selesai',  $d(10, 8),  $d(10, 12)],
            [4, 'SP-2023/10/085', 'Pengadaan Lisensi Software CAD Tambahan',  'draft',    null,       $d(10, 15)],
            [5, 'SP-2023/10/086', 'Jasa Kebersihan Berkala Gedung B dan C',   'diproses', $d(10, 10), $d(10, 15)],
            [6, 'SP-2023/10/087', 'Pengadaan AC Split Ruang Server Cabang',   'menunggu', $d(10, 15), $d(10, 15)],
            [7, 'SP-2023/10/088', 'Pemeliharaan Jaringan Internet Backup',    'selesai',  $d(10, 1),  $d(10, 5)],
            [8, 'SP-2023/10/089', 'Konsumsi Rapat Koordinasi Tahunan Unit',   'draft',    null,       $d(10, 16)],
        ];

        // Sisa 37 data: komposisi status dijaga agar total tab = 3 draft, 8 menunggu, 12 diproses, 22 selesai
        $judul = [
            'Pengadaan Meja dan Kursi Ruang Dosen', 'Pembelian Proyektor Ruang Kelas', 'Langganan Jurnal Ilmiah Internasional',
            'Perbaikan Atap Gedung Laboratorium', 'Pengadaan Reagen Laboratorium Kimia', 'Sewa Bus Kegiatan Kunjungan Industri',
            'Pengadaan Printer Multifungsi Unit', 'Pembelian Bahan Praktikum Elektro', 'Jasa Pemeliharaan Taman dan Lingkungan',
            'Pengadaan Buku Referensi Perpustakaan', 'Pembelian Perangkat Jaringan Switch', 'Jasa Percetakan Modul Perkuliahan',
            'Pengadaan Alat Keselamatan Laboratorium', 'Pembelian UPS Ruang Server Utama', 'Sewa Gedung Seminar Nasional',
            'Pengadaan Seragam Petugas Kebersihan', 'Perawatan Lift Gedung Dekanat', 'Pembelian Kamera Dokumentasi Kegiatan',
            'Pengadaan Perangkat Lunak Statistik', 'Jasa Konsultansi Akreditasi Program Studi',
        ];
        $sisa = ['draft' => 1, 'menunggu' => 6, 'diproses' => 10, 'selesai' => 20];
        $urutan = ['selesai', 'diproses', 'menunggu', 'selesai', 'draft'];

        for ($i = 0; $i < 37; $i++) {
            do {
                $status = $urutan[$i % count($urutan)];
                $urutan = array_merge(array_slice($urutan, 1), [$urutan[0]]);
            } while ($sisa[$status] === 0);
            $sisa[$status]--;

            $kirim = Carbon::create(2023, 9, 30, 9, 0)->subDays($i);
            $baris[] = [
                9 + $i,
                sprintf('SP-2023/09/%03d', 81 - $i),
                $judul[$i % count($judul)],
                $status,
                $status === 'draft' ? null : $kirim,
                $kirim->copy()->addDays(2),
            ];
        }

        return array_map(fn (array $b) => [
            'id' => $b[0], 'no_surat' => $b[1], 'judul' => $b[2], 'unit' => $unit,
            'status' => $b[3], 'kirim' => $b[4], 'update' => $b[5],
        ], $baris);
    }

    /** Lengkapi satu baris dasar dengan label, tanggal terformat, deskripsi, lampiran, dan log peninjauan */
    private static function lengkapi(array $b): array
    {
        $status = $b['status'];
        $kirim  = $b['kirim'] ?? null;

        $log = [['judul' => 'Pengajuan Selesai', 'keterangan' => ($kirim ? self::tgl($kirim) : '-') . ' - PPK Unit', 'state' => 'selesai']];

        switch ($status) {
            case 'draft':
                $log = [
                    ['judul' => 'Draft Disimpan', 'keterangan' => self::tgl($b['update']) . ' - PPK Unit', 'state' => 'selesai'],
                    ['judul' => 'Menunggu Pengiriman', 'keterangan' => 'Belum dikirim', 'state' => 'aktif'],
                ];
                break;
            case 'menunggu':
                $log[] = ['judul' => 'Approval Wadir 2', 'keterangan' => 'Menunggu Persetujuan', 'state' => 'aktif'];
                break;
            case 'diproses':
                $log[] = ['judul' => 'Disetujui Wadir 2', 'keterangan' => 'Wadir Bidang Umum', 'state' => 'selesai'];
                $log[] = ['judul' => 'Review Perencanaan', 'keterangan' => 'Sedang Berlangsung', 'state' => 'aktif'];
                break;
            case 'selesai':
                $log[] = ['judul' => 'Disetujui Wadir 2', 'keterangan' => 'Wadir Bidang Umum', 'state' => 'selesai'];
                $log[] = ['judul' => 'Pengadaan Selesai', 'keterangan' => self::tgl($b['update']), 'state' => 'selesai'];
                break;
        }

        return $b + [
            'label_status'  => self::LABEL_STATUS[$status],
            'tahap'         => self::LABEL_TAHAP[$status],
            'tgl_kirim'     => $kirim ? self::tgl($kirim) : '-',
            'tgl_update'    => self::tgl($b['update']),
            'tgl_kirim_lengkap' => $kirim ? self::tgl($kirim, 'd F Y') . ' - ' . $kirim->format('H:i') . ' WIB' : '-',
            'deskripsi'     => 'Kebutuhan ' . lcfirst($b['judul']) . ' diajukan untuk menunjang kelancaran kegiatan operasional dan akademik unit.',
            'lampiran'      => [
                ['nama' => 'RAB_Pengadaan.xlsx',           'ukuran' => '340 KB'],
                ['nama' => 'KAK_Spesifikasi_Teknis.pdf',   'ukuran' => '1.2 MB'],
                ['nama' => 'Surat_Permohonan_Ttd.pdf',     'ukuran' => '450 KB'],
            ],
            'log'           => $log,
        ];
    }

    /** Format tanggal Indonesia, contoh: "14 Feb 2026" */
    private static function tgl(Carbon $tanggal, string $format = 'd M Y'): string
    {
        return $tanggal->copy()->locale('id')->translatedFormat($format);
    }
}
