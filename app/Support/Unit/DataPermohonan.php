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
        'revisi'   => 'Revisi',
        'ditolak'  => 'Ditolak',
    ];

    /** Label tahap yang ditampilkan pada lencana di halaman Detail Permohonan */
    private const LABEL_TAHAP = [
        'draft'    => 'Draft',
        'menunggu' => 'Menunggu Approval',
        'diproses' => 'Sedang Ditinjau Perencanaan',
        'selesai'  => 'Pengadaan Selesai',
        'revisi'   => 'Perlu Revisi',
        'ditolak'  => 'Ditolak',
    ];

    /** Seluruh permohonan milik pengguna (45 data, id 1-45) */
    public static function semua(): Collection
    {
        static $cache = null;

        return $cache ??= collect(self::bangunDaftar())
            ->map(fn (array $baris) => self::lengkapi($baris));
    }

    /** 34 pengajuan untuk tabel "Daftar Pengajuan" di Dashboard (id 101-134). Lima baris pertama memakai teks contoh dari mockup V.2 */
    public static function dashboard(): Collection
    {
        static $cache = null;

        return $cache ??= self::bangunDashboard();
    }

    private static function bangunDashboard(): Collection
    {
        $unit = 'Fakultas Teknik - Perencanaan & IT';
        $judul = 'Pengadaan Komputer Lab Rekayasa Perangkat Lunak';
        $tanggal = Carbon::create(2026, 2, 14, 9, 30);

        $baris = [
            [101, '021/SPK/PHB-JBT/X/2026', $judul, 'revisi',   $tanggal],
            [102, '020/SPK/PHB-JBT/X/2026', $judul, 'diproses', $tanggal],
            [103, '019/SPK/PHB-JBT/X/2026', $judul, 'selesai',  $tanggal],
            [104, '018/SPK/PHB-JBT/X/2026', $judul, 'draft',    $tanggal],
            [105, '017/SPK/PHB-JBT/X/2026', $judul, 'ditolak',  $tanggal],
        ];

        // Baris ke-6 sampai ke-34 dibangkitkan agar paginasi (6 baris per halaman) bisa dicoba
        $urutanStatus = ['selesai', 'diproses', 'revisi', 'menunggu', 'draft', 'ditolak'];
        for ($i = 5; $i < 34; $i++) {
            $baris[] = [
                101 + $i,
                sprintf('%03d/SPK/PHB-JBT/X/2026', $i < 21 ? 21 - $i : 100 + $i),
                $judul,
                $urutanStatus[$i % count($urutanStatus)],
                $tanggal->copy()->subDays($i),
            ];
        }

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

            // Tanggal surat (baris kecil di bawah nomor surat pada tabel Dashboard)
            $data['tgl_surat'] = self::tgl(Carbon::create(2026, 10, 10));

            // Data lengkap untuk permohonan contoh pada mockup halaman Detail Permohonan
            if ($id === 101) {
                $data['deskripsi'] = 'Sebab spesifikasi komputer di Lab RPL saat ini masih menggunakan Core i3 generasi ke-4, tidak mumpuni lagi untuk menjalankan engine pengembangan modern serta IDE berat seperti Android Studio dan Visual Studio.';
                $data['lampiran'] = [
                    ['nama' => 'RAB_Pengadaan_Lab_RPL.xlsx',            'ukuran' => '340 KB'],
                    ['nama' => 'KAK_Spesifikasi_Teknis_Komputer.pdf',   'ukuran' => '1.2 MB'],
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

        $aktifKe = ['draft' => 0, 'menunggu' => 2, 'diproses' => 4, 'selesai' => 11, 'revisi' => 2, 'ditolak' => 2][$permohonan['status']];

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
            case 'revisi':
                $log[] = ['judul' => 'Perlu Revisi', 'keterangan' => 'Menunggu perbaikan dari pengusul', 'state' => 'aktif'];
                break;
            case 'ditolak':
                $log[] = ['judul' => 'Ditolak Wadir 2', 'keterangan' => 'Wadir Bidang Umum', 'state' => 'selesai'];
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

        // Status kartu persetujuan Wadir 2 dan Perencanaan mengikuti status permohonan (kunci: menunggu, disetujui, ditolak, revisi)
        [$statusWadir, $statusPerencanaan] = [
            'draft'    => ['menunggu',  'menunggu'],
            'menunggu' => ['menunggu',  'menunggu'],
            'diproses' => ['disetujui', 'menunggu'],
            'selesai'  => ['disetujui', 'disetujui'],
            'revisi'   => ['ditolak',   'revisi'],
            'ditolak'  => ['ditolak',   'menunggu'],
        ][$status];

        $persetujuan = [
            'wadir2' => [
                'judul'            => 'Wadir 2',
                'subjudul'         => 'Wakil Direktur 2',
                'status'           => $statusWadir,
                'penanggung_jawab' => 'Dr. Ir. H. Hermawan',
                'waktu'            => $statusWadir === 'disetujui' ? '15 Februari 2026' : 'Belum disetujui',
            ],
            'perencanaan' => [
                'judul'            => 'Perencanaan',
                'subjudul'         => 'Unit Perencanaan & IT',
                'status'           => $statusPerencanaan,
                'penanggung_jawab' => 'Dina Rahmawati',
                'waktu'            => $statusPerencanaan === 'disetujui' ? '16 Februari 2026' : 'Belum disetujui',
            ],
        ];

        $riwayat = [
            ['waktu' => '14 Feb 2026 • 09:30', 'user' => 'Hendra Wijaya',       'role' => 'PPK Unit',    'aktivitas' => 'Membuat pengajuan',            'sebelumnya' => 'Draf',     'baru' => 'Diajukan',  'catatan' => 'Dokumen awal diserahkan'],
            ['waktu' => '15 Feb 2026 • 10:45', 'user' => 'Dr. Ir. H. Hermawan', 'role' => 'Wadir 2',     'aktivitas' => 'Persetujuan Wadir 2',          'sebelumnya' => 'Menunggu', 'baru' => 'Disetujui', 'catatan' => 'Wadir 2 disetujui; global menunggu Perencanaan'],
            ['waktu' => '15 Feb 2026 • 11:10', 'user' => 'Dina Rahmawati',      'role' => 'Perencanaan', 'aktivitas' => 'Membuka tinjauan anggaran',    'sebelumnya' => 'Menunggu', 'baru' => 'Menunggu', 'catatan' => 'Verifikasi RAB sedang berlangsung'],
            ['waktu' => '16 Feb 2026 • 08:20', 'user' => 'Dina Rahmawati',      'role' => 'Perencanaan', 'aktivitas' => 'Menambahkan catatan verifikasi', 'sebelumnya' => 'Menunggu', 'baru' => 'Menunggu', 'catatan' => 'Butuh klarifikasi spesifikasi RAM'],
        ];

        return $b + [
            'label_status'  => self::LABEL_STATUS[$status],
            'tahap'         => self::LABEL_TAHAP[$status],
            'nilai_anggaran' => 'Rp. 100.000.000,00',
            'permintaan_perbaikan' => [
                'diminta_oleh'     => 'Budi Setiawan',
                'peninjau'         => 'Wadir 2 — Budi Setiawan',
                'waktu'            => '14 Oktober 2026 09:30 WIB',
                'penanggung_jawab' => 'User Unit — Mahfudh Al Rafif',
                'catatan'          => 'Lengkapi klausul garansi minimum 3 tahun dan rincian layanan purna jual pada spesifikasi teknis.',
            ],
            'persetujuan'   => $persetujuan,
            'riwayat'       => $riwayat,
            'tgl_kirim'     => $kirim ? self::tgl($kirim) : '-',
            'tgl_update'    => self::tgl($b['update']),
            'tgl_kirim_lengkap' => $kirim ? self::tgl($kirim, 'd F Y') . ' - ' . $kirim->format('H:i') . ' WIB' : '-',
            'deskripsi'     => 'Kebutuhan ' . lcfirst($b['judul']) . ' diajukan untuk menunjang kelancaran kegiatan operasional dan akademik unit.',
            'lampiran'      => [
                ['nama' => 'RAB_Pengadaan.xlsx',           'ukuran' => '340 KB'],
                ['nama' => 'KAK_Spesifikasi_Teknis.pdf',   'ukuran' => '1.2 MB'],
            ],
            'log'           => $log,
        ];
    }

    /**
     * Alur Perjalanan Pengadaan (10 langkah) untuk modal Lacak Pengajuan.
     * Status tiap langkah diturunkan dari status permohonan: selesai (hijau), aktif (biru),
     * menunggu / "Yang akan datang" (abu-abu), atau ditolak (merah).
     *
     * @return array<int, array{nomor:string, judul:string, peran:?string, keterangan:string, state:string}>
     */
    public static function alur(array $permohonan): array
    {
        $status = $permohonan['status'];

        // Indeks langkah yang sedang berjalan; null berarti seluruh langkah sudah selesai
        $aktifKe = ['draft' => 0, 'menunggu' => 1, 'diproses' => 3, 'revisi' => 2, 'ditolak' => 1, 'selesai' => null][$status];

        // [nomor, judul, peran, keterangan saat selesai, keterangan saat aktif]
        $langkah = [
            ['1.',     'Permohonan Diajukan',                          'Hendra Wijaya / PPK Pengusul',         'Diajukan pada 14 Feb 2026 - 09:30 WIB',   'Permohonan belum diajukan.'],
            ['2.',     'Persetujuan Wadir 2',                          'Dr. Ir. H. Hermawan / Wadir 2',        'Disetujui pada 15 Feb 2026 - 11:20 WIB',  'Menunggu persetujuan Wadir 2.'],
            ['3.1',    'Perencanaan/MAK: Review',                      'Dina Rahmawati / Perencanaan',         'Disetujui pada 16 Feb 2026 - 14:00 WIB',  'Perlu revisi dari pengusul sebelum ditinjau kembali.'],
            ['3.2',    'Perencanaan/MAK: Penentuan MAK',               'Dina Rahmawati / Perencanaan',         'Selesai ditentukan.',                      'Sedang diproses. Wadir 2 DAN Perencanaan wajib Disetujui sebelum PPBJ.'],
            ['4.',     'PPBJ',                                         'Pokja / Tim Teknis',                   'Selesai.',                                 'Sedang diproses oleh Pokja / Tim Teknis.'],
            ['5.',     'Review PP/PPK jika berlaku',                   'Routing dan penugasan PPK (>Rp200 juta)', 'Selesai.',                             'Sedang ditinjau.'],
            ['6-7.',   'Proses Pengadaan dan Kontrak/SPK',             'Vendor Partner',                       'Selesai.',                                 'Sedang diproses.'],
            ['8-9.',   'Pengiriman dan Penerimaan Barang',             'Panitia Penerima Hasil Pekerjaan',     'Selesai.',                                 'Sedang diproses.'],
            ['10-14.', 'Dokumen, Persetujuan, dan Proses Pembayaran',  null,                                   'Selesai.',                                 'Sedang diproses.'],
            ['15.',    'Pembayaran Selesai',                           'Sistem Administrasi PBJ',              'Selesai.',                                 'Sedang diproses.'],
        ];

        return collect($langkah)->map(function (array $l, int $indeks) use ($aktifKe, $status) {
            $state = $aktifKe === null || $indeks < $aktifKe ? 'selesai' : ($indeks === $aktifKe ? 'aktif' : 'menunggu');

            if ($state === 'aktif' && $status === 'ditolak') {
                $state = 'ditolak';
                $l[4] = 'Ditolak oleh Wadir 2.';
            }

            return [
                'nomor'      => $l[0],
                'bullet'     => $indeks >= 4,
                'judul'      => $l[1],
                'peran'      => $l[2],
                'keterangan' => match ($state) {
                    'selesai' => $l[3],
                    'menunggu' => 'Yang akan datang',
                    default   => $l[4],
                },
                'state'      => $state,
            ];
        })->all();
    }

    /** Format tanggal Indonesia, contoh: "14 Feb 2026" */
    private static function tgl(Carbon $tanggal, string $format = 'd M Y'): string
    {
        return $tanggal->copy()->locale('id')->translatedFormat($format);
    }
}
