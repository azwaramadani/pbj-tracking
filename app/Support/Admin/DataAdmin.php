<?php

namespace App\Support\Admin;

/**
 * Sumber data contoh (dummy) untuk seluruh halaman daftar role Super Admin: Dashboard dan 13 halaman master/transaksi/sistem.
 *
 * Setiap bagian berisi:
 *   judul, deskripsi, objek (nama tunggal untuk judul modal), kartu (4 kartu ringkasan), kolom (definisi kolom tabel),
 *   baris (data), tambah (tampilkan tombol "+ Tambah"), dan opsional detail (isian modal) serta filter (kunci kolom filter).
 *
 * Definisi kolom: ['label', 'tipe', 'kunci', 'pil' => peta nilai => warna]
 *   tipe 'utama' = teks tebal + keterangan kecil "Data contoh"; 'teks' = teks biasa;
 *   tipe 'pil'   = pil berwarna bila nilainya ada di peta 'pil' (hijau/kuning/merah/biru/abu), selain itu teks kapital biasa.
 *
 * TODO Backend: nilai-nilai di sini harus diturunkan dari file migrasi tiap sub-modul (kolom tabel = kolom migrasi,
 * kartu ringkasan = agregat query). Mis. Manajemen Pengguna -> tabel users (name, email, ...) + kolom NIP, unit, role, status
 * yang perlu ditambahkan lewat migrasi baru karena tabel users saat ini hanya punya name, email, password.
 */
class DataAdmin
{
    /** Nama bagian (slug URL) menurut urutan sidebar */
    public const BAGIAN = [
        'pengguna', 'role', 'unit', 'mak', 'pengajuan', 'pengadaan', 'dokumen',
        'approval', 'reminder', 'penerimaan', 'pembayaran', 'notifikasi', 'audit',
    ];

    private const STATUS_AKTIF = ['Aktif' => 'hijau', 'Nonaktif' => 'merah'];

    /** Dashboard: tiga baris ringkasan; tombol Detail menuju Detail Tracking (id 601-603 milik App\Support\Ppk\DataTracking) */
    public static function dashboard(): array
    {
        return [
            'judul'     => 'Dashboard',
            'deskripsi' => 'Ringkasan administrasi dan pengelolaan SIMPATIK-PBJ.',
            'menu'      => 'dashboard',
            'tambah'    => false,
            'filter'    => 'modul',
            'kartu'     => self::kartu([[8, 'Pengajuan Surat'], [34, 'Paket Pengadaan'], [6, 'Menunggu Tindak Lanjut'], [126, 'Total Data Terkelola']]),
            'kolom'     => [
                ['label' => 'Modul',  'tipe' => 'utama', 'kunci' => 'modul'],
                ['label' => 'Total',  'tipe' => 'teks',  'kunci' => 'total'],
                ['label' => 'Status', 'tipe' => 'teks',  'kunci' => 'status'],
            ],
            'baris'     => [
                ['id' => 601, 'modul' => 'Pengajuan surat', 'total' => '021/SPK/PHB-JBT/X/2026', 'status' => 'Fakultas Teknik'],
                ['id' => 602, 'modul' => 'Paket pengadaan', 'total' => 'PBJ-2026-001',           'status' => 'Pengadaan Komputer Lab'],
                ['id' => 603, 'modul' => 'Pembayaran',      'total' => 'INV-2026-011',           'status' => 'PT Mitra Jaya'],
            ],
        ];
    }

    /** Satu bagian halaman daftar menurut slug; null jika slug tidak dikenal */
    public static function bagian(string $slug): ?array
    {
        return match ($slug) {
            'pengguna'   => self::pengguna(),
            'role'       => self::role(),
            'unit'       => self::unit(),
            'mak'        => self::mak(),
            'pengajuan'  => self::pengajuan(),
            'pengadaan'  => self::pengadaan(),
            'dokumen'    => self::dokumen(),
            'approval'   => self::approval(),
            'reminder'   => self::reminder(),
            'penerimaan' => self::penerimaan(),
            'pembayaran' => self::pembayaran(),
            'notifikasi' => self::notifikasi(),
            'audit'      => self::audit(),
            default      => null,
        };
    }

    private static function kartu(array $daftar): array
    {
        return array_map(fn (array $k) => ['jumlah' => $k[0], 'label' => $k[1]], $daftar);
    }

    private static function pengguna(): array
    {
        return [
            'judul' => 'Manajemen Pengguna', 'deskripsi' => 'Kelola akun, unit, dan role pengguna aplikasi.',
            'objek' => 'Pengguna', 'menu' => 'pengguna', 'tambah' => true,
            'kartu' => self::kartu([[24, 'Total Pengguna'], [22, 'Akun Aktif'], [2, 'Nonaktif'], [9, 'Role Terdaftar']]),
            'kolom' => [
                ['label' => 'Nama Pengguna', 'tipe' => 'utama', 'kunci' => 'nama'],
                ['label' => 'Email',         'tipe' => 'teks',  'kunci' => 'email'],
                ['label' => 'Unit',          'tipe' => 'teks',  'kunci' => 'unit'],
                ['label' => 'Role',          'tipe' => 'teks',  'kunci' => 'role'],
                ['label' => 'Status',        'tipe' => 'pil',   'kunci' => 'status', 'pil' => self::STATUS_AKTIF],
            ],
            // Isian modal Detail Pengguna (urut sesuai mockup); 'opsi' menjadikan isian dropdown, 'tetap' = tidak bisa diubah saat mode edit
            'detail' => [
                ['label' => 'ID Pengguna', 'kunci' => 'id',     'tetap' => true],
                ['label' => 'Nama',        'kunci' => 'nama'],
                ['label' => 'Email',       'kunci' => 'email',  'jenis' => 'email'],
                ['label' => 'NIP',         'kunci' => 'nip'],
                ['label' => 'Unit',        'kunci' => 'unit'],
                ['label' => 'Role',        'kunci' => 'role',   'opsi' => ['Super Admin', 'User Unit', 'Wadir 2', 'PP', 'PPK', 'PPBJ', 'Perencanaan', 'Penerimaan', 'Keuangan']],
                ['label' => 'Status',      'kunci' => 'status', 'opsi' => ['Aktif', 'Nonaktif']],
            ],
            'baris' => [
                ['id' => 'USR-2026-0001', 'nama' => 'Mahfudh Al Rafif',   'email' => 'mahfudh@simp.pbj.id', 'nip' => '198503212010121002', 'unit' => 'Administrasi',    'role' => 'Super Admin', 'status' => 'Aktif'],
                ['id' => 'USR-2026-0002', 'nama' => 'Rian Hidayat, S.T.', 'email' => 'rian@simp.pbj.id',    'nip' => '199001152015011003', 'unit' => 'Fakultas Teknik', 'role' => 'User Unit',   'status' => 'Aktif'],
                ['id' => 'USR-2026-0003', 'nama' => 'Dewi Anggraini',     'email' => 'dewi@simp.pbj.id',    'nip' => '199205202018012001', 'unit' => 'Perencanaan',     'role' => 'Perencanaan', 'status' => 'Aktif'],
                ['id' => 'USR-2026-0004', 'nama' => 'Siti Rahma',         'email' => 'siti@simp.pbj.id',    'nip' => '198811302012122001', 'unit' => 'Pengadaan',       'role' => 'PPBJ',        'status' => 'Nonaktif'],
            ],
        ];
    }

    private static function role(): array
    {
        return [
            'judul' => 'Role & Hak Akses', 'deskripsi' => 'Atur role dan cakupan akses menu untuk pengguna.',
            'objek' => 'Role', 'menu' => 'role', 'tambah' => true,
            'kartu' => self::kartu([[9, 'Total Role'], [9, 'Role Aktif'], [14, 'Menu Sistem'], [3, 'Akses Khusus']]),
            'kolom' => [
                ['label' => 'Nama Role', 'tipe' => 'utama', 'kunci' => 'nama'],
                ['label' => 'Kode',      'tipe' => 'teks',  'kunci' => 'kode'],
                ['label' => 'Deskripsi', 'tipe' => 'teks',  'kunci' => 'deskripsi'],
                ['label' => 'Hak Akses', 'tipe' => 'teks',  'kunci' => 'hak_akses'],
                ['label' => 'Status',    'tipe' => 'pil',   'kunci' => 'status', 'pil' => self::STATUS_AKTIF],
            ],
            'baris' => [
                ['nama' => 'User Unit', 'kode' => 'ROLE-01', 'deskripsi' => 'Mengajukan permohonan',        'hak_akses' => 'Permohonan, Riwayat',  'status' => 'Aktif'],
                ['nama' => 'Wadir 2',   'kode' => 'ROLE-02', 'deskripsi' => 'Persetujuan permohonan',       'hak_akses' => 'Approval, Riwayat',    'status' => 'Aktif'],
                ['nama' => 'PPK',       'kode' => 'ROLE-03', 'deskripsi' => 'Review dan memo pengadaan',    'hak_akses' => 'Approval, Tracking',   'status' => 'Aktif'],
                ['nama' => 'PPBJ',      'kode' => 'ROLE-04', 'deskripsi' => 'Metode pengadaan dan kontrak', 'hak_akses' => 'Kontrak, Pembayaran', 'status' => 'Aktif'],
            ],
        ];
    }

    private static function unit(): array
    {
        return [
            'judul' => 'Unit / Fakultas', 'deskripsi' => 'Kelola unit pengaju dan unit kerja.',
            'objek' => 'Unit', 'menu' => 'unit', 'tambah' => true,
            'kartu' => self::kartu([[12, 'Total Unit'], [11, 'Unit Aktif'], [1, 'Unit Nonaktif'], [8, 'Pengajuan Aktif']]),
            'kolom' => [
                ['label' => 'Kode',               'tipe' => 'utama', 'kunci' => 'kode'],
                ['label' => 'Nama Unit/Fakultas', 'tipe' => 'teks',  'kunci' => 'nama'],
                ['label' => 'Pimpinan',           'tipe' => 'teks',  'kunci' => 'pimpinan'],
                ['label' => 'Email Unit',         'tipe' => 'teks',  'kunci' => 'email'],
                ['label' => 'Status',             'tipe' => 'pil',   'kunci' => 'status', 'pil' => self::STATUS_AKTIF],
            ],
            'baris' => [
                ['kode' => 'FT',    'nama' => 'Fakultas Teknik',      'pimpinan' => 'Dekan Fakultas Teknik',    'email' => 'ft@simp.pbj.id',    'status' => 'Aktif'],
                ['kode' => 'FISIP', 'nama' => 'Fakultas Ilmu Sosial', 'pimpinan' => 'Dekan FISIP',              'email' => 'fisip@simp.pbj.id', 'status' => 'Aktif'],
                ['kode' => 'BAU',   'nama' => 'Biro Administrasi',    'pimpinan' => 'Kepala Biro Administrasi', 'email' => 'bau@simp.pbj.id',   'status' => 'Aktif'],
            ],
        ];
    }

    private static function mak(): array
    {
        return [
            'judul' => 'Mata Anggaran (MAK)', 'deskripsi' => 'Kelola referensi mata anggaran kegiatan.',
            'objek' => 'Mata Anggaran', 'menu' => 'mak', 'tambah' => true,
            'kartu' => self::kartu([[18, 'Total MAK'], [17, 'MAK Aktif'], ['Rp 2,4 M', 'Total Pagu'], [2026, 'Tahun Anggaran']]),
            'kolom' => [
                ['label' => 'Kode MAK',           'tipe' => 'utama', 'kunci' => 'kode'],
                ['label' => 'Nama Mata Anggaran', 'tipe' => 'teks',  'kunci' => 'nama'],
                ['label' => 'Jenis',              'tipe' => 'teks',  'kunci' => 'jenis'],
                ['label' => 'Pagu Anggaran',      'tipe' => 'teks',  'kunci' => 'pagu'],
                ['label' => 'Tahun',              'tipe' => 'teks',  'kunci' => 'tahun'],
                ['label' => 'Status',             'tipe' => 'pil',   'kunci' => 'status', 'pil' => self::STATUS_AKTIF],
            ],
            'baris' => [
                ['kode' => '02.01.04.12.001', 'nama' => 'Belanja Modal Komputer Lab', 'jenis' => 'Rupiah Murni', 'pagu' => 'Rp 450.000.000', 'tahun' => '2026', 'status' => 'Aktif'],
                ['kode' => '02.01.04.12.002', 'nama' => 'Belanja Peralatan Jaringan', 'jenis' => 'PNBP',         'pagu' => 'Rp 180.000.000', 'tahun' => '2026', 'status' => 'Aktif'],
                ['kode' => '02.01.04.12.003', 'nama' => 'Belanja Perangkat Lunak',    'jenis' => 'PNBP',         'pagu' => 'Rp 90.000.000',  'tahun' => '2026', 'status' => 'Aktif'],
            ],
        ];
    }

    private static function pengajuan(): array
    {
        $pil = ['Diproses' => 'kuning', 'Perlu Revisi' => 'kuning', 'Selesai' => 'hijau', 'Ditolak' => 'merah'];

        return [
            'judul' => 'Pengajuan Surat', 'deskripsi' => 'Kelola surat permohonan, pemohon, dan status pengajuan.',
            'objek' => 'Pengajuan', 'menu' => 'pengajuan', 'tambah' => true,
            'kartu' => self::kartu([[34, 'Total Pengajuan'], [8, 'Menunggu Approval'], [3, 'Perlu Direvisi'], [2, 'Ditolak']]),
            'kolom' => [
                ['label' => 'No. Surat',       'tipe' => 'utama', 'kunci' => 'no_surat'],
                ['label' => 'Unit Pengaju',    'tipe' => 'teks',  'kunci' => 'unit'],
                ['label' => 'Judul Pengadaan', 'tipe' => 'teks',  'kunci' => 'judul'],
                ['label' => 'Nilai Estimasi',  'tipe' => 'teks',  'kunci' => 'nilai'],
                ['label' => 'Status',          'tipe' => 'pil',   'kunci' => 'status', 'pil' => $pil],
            ],
            'baris' => [
                ['no_surat' => '021/SPK/PHB-JBT/X/2026', 'unit' => 'Fakultas Teknik', 'judul' => 'Pengadaan Komputer Lab',       'nilai' => 'Rp 120.000.000', 'status' => 'Diproses'],
                ['no_surat' => '022/SPK/PHB-JBT/X/2026', 'unit' => 'Fakultas Teknik', 'judul' => 'Pengadaan Peralatan Jaringan', 'nilai' => 'Rp 320.000.000', 'status' => 'Perlu Revisi'],
                ['no_surat' => '023/SPK/PHB-JBT/X/2026', 'unit' => 'FISIP',           'judul' => 'Pengadaan Perangkat Lunak',   'nilai' => 'Rp 90.000.000',  'status' => 'Selesai'],
                ['no_surat' => '024/SPK/PHB-JBT/X/2026', 'unit' => 'FISIP',           'judul' => 'Pengadaan Furnitur Kantor',   'nilai' => 'Rp 60.000.000',  'status' => 'Ditolak'],
            ],
        ];
    }

    private static function pengadaan(): array
    {
        return [
            'judul' => 'Data Pengadaan', 'deskripsi' => 'Kelola paket pengadaan dan perkembangan proses.',
            'objek' => 'Pengadaan', 'menu' => 'pengadaan', 'tambah' => false,
            'kartu' => self::kartu([[34, 'Total Paket'], [12, 'Sedang Berjalan'], [8, 'Barang Diterima'], [14, 'Selesai']]),
            'kolom' => [
                ['label' => 'Kode Paket',  'tipe' => 'utama', 'kunci' => 'kode'],
                ['label' => 'Nomor Surat', 'tipe' => 'teks',  'kunci' => 'no_surat'],
                ['label' => 'Nama Paket',  'tipe' => 'teks',  'kunci' => 'nama'],
                ['label' => 'Metode',      'tipe' => 'teks',  'kunci' => 'metode'],
                ['label' => 'Nilai',       'tipe' => 'teks',  'kunci' => 'nilai'],
                ['label' => 'Status',      'tipe' => 'pil',   'kunci' => 'status', 'pil' => ['Review' => 'kuning', 'Selesai' => 'hijau']],
            ],
            'baris' => [
                ['kode' => 'PBJ-2026-001', 'no_surat' => '021/SPK/PHB-JBT/X/2026', 'nama' => 'Pengadaan Komputer Lab',       'metode' => 'Pengadaan langsung', 'nilai' => 'Rp 120.000.000', 'status' => 'Review'],
                ['kode' => 'PBJ-2026-002', 'no_surat' => '022/SPK/PHB-JBT/X/2026', 'nama' => 'Pengadaan Peralatan Jaringan', 'metode' => 'Tender / Seleksi',   'nilai' => 'Rp 320.000.000', 'status' => 'Berjalan'],
                ['kode' => 'PBJ-2026-003', 'no_surat' => '023/SPK/PHB-JBT/X/2026', 'nama' => 'Pengadaan Perangkat Lunak',   'metode' => 'Pengadaan langsung', 'nilai' => 'Rp 90.000.000',  'status' => 'Selesai'],
            ],
        ];
    }

    private static function dokumen(): array
    {
        $pil = ['Terverifikasi' => 'hijau', 'Menunggu' => 'kuning', 'Perlu Perbaikan' => 'merah'];

        return [
            'judul' => 'Dokumen & Lampiran', 'deskripsi' => 'Kelola metadata file yang terkait pengajuan dan pengadaan.',
            'objek' => 'Dokumen', 'menu' => 'dokumen', 'tambah' => false,
            'kartu' => self::kartu([[126, 'Total Dokumen'], [102, 'Terverifikasi'], [18, 'Menunggu Verifikasi'], [6, 'Perlu Perbaikan']]),
            'kolom' => [
                ['label' => 'Kode Dokumen', 'tipe' => 'utama', 'kunci' => 'kode'],
                ['label' => 'Referensi',    'tipe' => 'teks',  'kunci' => 'referensi'],
                ['label' => 'Kategori',     'tipe' => 'teks',  'kunci' => 'kategori'],
                ['label' => 'Nama File',    'tipe' => 'teks',  'kunci' => 'file'],
                ['label' => 'Tanggal',      'tipe' => 'teks',  'kunci' => 'tanggal'],
                ['label' => 'Status',       'tipe' => 'pil',   'kunci' => 'status', 'pil' => $pil],
            ],
            'baris' => [
                ['kode' => 'DOC-2026-0089', 'referensi' => 'PBJ-2026-001', 'kategori' => 'Surat Permohonan',   'file' => 'Surat_Permohonan_FT.pdf',     'tanggal' => '10 Okt 2026', 'status' => 'Terverifikasi'],
                ['kode' => 'DOC-2026-0090', 'referensi' => 'PBJ-2026-001', 'kategori' => 'Spesifikasi Teknis', 'file' => 'Spesifikasi_Komputer.pdf',   'tanggal' => '10 Okt 2026', 'status' => 'Menunggu'],
                ['kode' => 'DOC-2026-0091', 'referensi' => 'PBJ-2026-002', 'kategori' => 'Berita Acara',       'file' => 'BA_Penerimaan_Jaringan.pdf',  'tanggal' => '15 Okt 2026', 'status' => 'Terverifikasi'],
            ],
        ];
    }

    private static function approval(): array
    {
        $keputusan = ['Menunggu' => 'kuning', 'Perlu Revisi' => 'kuning', 'Disetujui' => 'hijau', 'Ditolak' => 'merah'];

        return [
            'judul' => 'Riwayat Approval', 'deskripsi' => 'Pantau catatan keputusan persetujuan setiap tahapan.',
            'objek' => 'Approval', 'menu' => 'approval', 'tambah' => false, 'filter' => 'keputusan',
            'kartu' => self::kartu([[76, 'Total Approval'], [8, 'Menunggu'], [62, 'Disetujui'], [6, 'Perlu Revisi']]),
            'kolom' => [
                ['label' => 'Referensi', 'tipe' => 'utama', 'kunci' => 'referensi'],
                ['label' => 'Tahap',     'tipe' => 'teks',  'kunci' => 'tahap'],
                ['label' => 'Pelaku',    'tipe' => 'teks',  'kunci' => 'pelaku'],
                ['label' => 'Keputusan', 'tipe' => 'pil',   'kunci' => 'keputusan', 'pil' => $keputusan],
                ['label' => 'Tanggal',   'tipe' => 'teks',  'kunci' => 'tanggal'],
                ['label' => 'Catatan',   'tipe' => 'pil',   'kunci' => 'catatan', 'pil' => ['Menunggu pemeriksaan' => 'kuning'], 'huruf' => false],
            ],
            'baris' => [
                ['referensi' => '021/SPK/PHB-JBT/X/2026', 'tahap' => 'Wadir 2',     'pelaku' => 'Wadir 2',        'keputusan' => 'Menunggu',     'tanggal' => '14 Okt 2026', 'catatan' => 'Menunggu pemeriksaan'],
                ['referensi' => '022/SPK/PHB-JBT/X/2026', 'tahap' => 'Perencanaan', 'pelaku' => 'Dewi Anggraini', 'keputusan' => 'Perlu Revisi', 'tanggal' => '13 Okt 2026', 'catatan' => 'Lampiran anggaran'],
                ['referensi' => '023/SPK/PHB-JBT/X/2026', 'tahap' => 'PPK',         'pelaku' => 'Dina Rahmawati', 'keputusan' => 'Disetujui',    'tanggal' => '12 Okt 2026', 'catatan' => 'Sesuai ketentuan'],
            ],
        ];
    }

    private static function reminder(): array
    {
        return [
            'judul' => 'Reminder', 'deskripsi' => 'Atur pengingat jatuh tempo dan penerima notifikasi.',
            'objek' => 'Reminder', 'menu' => 'reminder', 'tambah' => false,
            'kartu' => self::kartu([[18, 'Total Reminder'], [7, 'Terjadwal'], [9, 'Terkirim'], [2, 'Jatuh Tempo']]),
            'kolom' => [
                ['label' => 'Referensi',       'tipe' => 'utama', 'kunci' => 'referensi'],
                ['label' => 'Judul Pengingat', 'tipe' => 'teks',  'kunci' => 'judul'],
                ['label' => 'Tanggal',         'tipe' => 'teks',  'kunci' => 'tanggal'],
                ['label' => 'Penerima',        'tipe' => 'teks',  'kunci' => 'penerima'],
                ['label' => 'Jenis',           'tipe' => 'teks',  'kunci' => 'jenis'],
                ['label' => 'Status',          'tipe' => 'pil',   'kunci' => 'status', 'pil' => []],
            ],
            'baris' => [
                ['referensi' => 'PBJ-2026-001', 'judul' => 'Pengingat jatuh tempo SPK',   'tanggal' => '20 Okt 2026', 'penerima' => 'PPBJ, Perlengkapan', 'jenis' => 'H-2',     'status' => 'Terjadwal'],
                ['referensi' => 'PBJ-2026-002', 'judul' => 'Pengingat dokumen tagihan',   'tanggal' => '22 Okt 2026', 'penerima' => 'PPBJ',               'jenis' => 'Dokumen', 'status' => 'Terkirim'],
                ['referensi' => 'PBJ-2026-003', 'judul' => 'Pengingat pengiriman barang', 'tanggal' => '25 Okt 2026', 'penerima' => 'Penerimaan',         'jenis' => 'H-1',     'status' => 'Terjadwal'],
            ],
        ];
    }

    private static function penerimaan(): array
    {
        $pil = ['Terverifikasi' => 'hijau', 'Menunggu' => 'kuning', 'Perlu Perbaikan' => 'merah'];

        return [
            'judul' => 'Penerimaan Barang', 'deskripsi' => 'Catat penerimaan, pemeriksaan barang, dan dokumen BAST.',
            'objek' => 'Penerimaan', 'menu' => 'penerimaan', 'tambah' => false,
            'kartu' => self::kartu([[18, 'Total Penerimaan'], [13, 'Terverifikasi'], [4, 'Menunggu Verifikasi'], [1, 'Perlu Perbaikan']]),
            'kolom' => [
                ['label' => 'Referensi',      'tipe' => 'utama', 'kunci' => 'referensi'],
                ['label' => 'Barang / Paket', 'tipe' => 'teks',  'kunci' => 'barang'],
                ['label' => 'Tanggal',        'tipe' => 'teks',  'kunci' => 'tanggal'],
                ['label' => 'Petugas',        'tipe' => 'teks',  'kunci' => 'petugas'],
                ['label' => 'Kondisi',        'tipe' => 'teks',  'kunci' => 'kondisi'],
                ['label' => 'Status',         'tipe' => 'pil',   'kunci' => 'status', 'pil' => $pil],
            ],
            'baris' => [
                ['referensi' => 'PBJ-2026-002', 'barang' => 'Peralatan jaringan', 'tanggal' => '15 Okt 2026', 'petugas' => 'Tim Perlengkapan', 'kondisi' => 'Sesuai', 'status' => 'Terverifikasi'],
                ['referensi' => 'PBJ-2026-004', 'barang' => 'Komputer Lab',        'tanggal' => '16 Okt 2026', 'petugas' => 'Tim Perlengkapan', 'kondisi' => 'Sesuai', 'status' => 'Menunggu'],
                ['referensi' => 'PBJ-2026-005', 'barang' => 'Perangkat lunak',     'tanggal' => '17 Okt 2026', 'petugas' => 'Tim Perlengkapan', 'kondisi' => 'Sesuai', 'status' => 'Menunggu'],
            ],
        ];
    }

    private static function pembayaran(): array
    {
        return [
            'judul' => 'Pembayaran', 'deskripsi' => 'Kelola status tagihan, persetujuan, dan bukti pembayaran.',
            'objek' => 'Pembayaran', 'menu' => 'pembayaran', 'tambah' => false,
            'kartu' => self::kartu([[18, 'Total Tagihan'], [4, 'Menunggu Persetujuan'], [3, 'Diproses'], [11, 'Sudah Dibayar']]),
            'kolom' => [
                ['label' => 'Referensi',     'tipe' => 'utama', 'kunci' => 'referensi'],
                ['label' => 'Penyedia',      'tipe' => 'teks',  'kunci' => 'penyedia'],
                ['label' => 'Nilai Tagihan', 'tipe' => 'teks',  'kunci' => 'nilai'],
                ['label' => 'Tgl Tagihan',   'tipe' => 'teks',  'kunci' => 'tanggal'],
                ['label' => 'Metode',        'tipe' => 'teks',  'kunci' => 'metode'],
                ['label' => 'Status',        'tipe' => 'pil',   'kunci' => 'status', 'pil' => ['Menunggu' => 'kuning', 'Diproses' => 'kuning', 'Dibayar' => 'hijau']],
            ],
            'baris' => [
                ['referensi' => 'INV-2026-011', 'penyedia' => 'PT Mitra Jaya',        'nilai' => 'Rp 320.000.000', 'tanggal' => '16 Okt 2026', 'metode' => 'Transfer', 'status' => 'Menunggu'],
                ['referensi' => 'INV-2026-012', 'penyedia' => 'CV Sinar Teknik',      'nilai' => 'Rp 120.000.000', 'tanggal' => '17 Okt 2026', 'metode' => 'Transfer', 'status' => 'Diproses'],
                ['referensi' => 'INV-2026-013', 'penyedia' => 'PT Nusantara Digital', 'nilai' => 'Rp 90.000.000',  'tanggal' => '18 Okt 2026', 'metode' => 'Transfer', 'status' => 'Dibayar'],
            ],
        ];
    }

    private static function notifikasi(): array
    {
        return [
            'judul' => 'Notifikasi', 'deskripsi' => 'Kelola notifikasi sistem dan status keterbacaan.',
            'objek' => 'Notifikasi', 'menu' => 'notifikasi', 'tambah' => false,
            'kartu' => self::kartu([[17, 'Total Notifikasi'], [10, 'Belum Dibaca'], [6, 'Approval'], [3, 'Reminder']]),
            'kolom' => [
                ['label' => 'Judul Notifikasi', 'tipe' => 'utama', 'kunci' => 'judul'],
                ['label' => 'Penerima',         'tipe' => 'teks',  'kunci' => 'penerima'],
                ['label' => 'Kategori',         'tipe' => 'teks',  'kunci' => 'kategori'],
                ['label' => 'Tanggal',          'tipe' => 'teks',  'kunci' => 'tanggal'],
                ['label' => 'Status',           'tipe' => 'pil',   'kunci' => 'status', 'pil' => []],
            ],
            'baris' => [
                ['judul' => 'Persetujuan Permohonan Baru',     'penerima' => 'Wadir 2',  'kategori' => 'Approval',   'tanggal' => '15 Okt 2026', 'status' => 'Belum dibaca'],
                ['judul' => 'Penerbitan Kontrak Pengadaan',    'penerima' => 'PPBJ',     'kategori' => 'Kontrak',    'tanggal' => '15 Okt 2026', 'status' => 'Belum dibaca'],
                ['judul' => 'Perintah Pembayaran Diterbitkan', 'penerima' => 'Keuangan', 'kategori' => 'Pembayaran', 'tanggal' => '16 Okt 2026', 'status' => 'Dibaca'],
            ],
        ];
    }

    private static function audit(): array
    {
        return [
            'judul' => 'Audit Log / Riwayat', 'deskripsi' => 'Pantau catatan aktivitas dan perubahan data di dalam aplikasi.',
            'objek' => 'Aktivitas', 'menu' => 'audit', 'tambah' => false,
            'kartu' => self::kartu([[126, 'Total Aktivitas'], [119, 'Berhasil'], [2, 'Gagal'], [5, 'Perlu Tindak Lanjut']]),
            'kolom' => [
                ['label' => 'Waktu',     'tipe' => 'utama', 'kunci' => 'waktu'],
                ['label' => 'Pengguna',  'tipe' => 'teks',  'kunci' => 'pengguna'],
                ['label' => 'Role',      'tipe' => 'teks',  'kunci' => 'role'],
                ['label' => 'Aktivitas', 'tipe' => 'teks',  'kunci' => 'aktivitas'],
                ['label' => 'Modul',     'tipe' => 'teks',  'kunci' => 'modul'],
                ['label' => 'Hasil',     'tipe' => 'pil',   'kunci' => 'hasil', 'pil' => ['Berhasil' => 'hijau', 'Gagal' => 'merah']],
            ],
            'baris' => [
                ['waktu' => '15 Okt 2026, 08:50', 'pengguna' => 'Rian Hidayat',   'role' => 'User Unit',    'aktivitas' => 'Mengunggah surat',     'modul' => 'Pengajuan Surat', 'hasil' => 'Berhasil'],
                ['waktu' => '14 Okt 2026, 14:20', 'pengguna' => 'Wadir 2',        'role' => 'Wadir 2',      'aktivitas' => 'Membuka detail',       'modul' => 'Approval',        'hasil' => 'Berhasil'],
                ['waktu' => '14 Okt 2026, 09:05', 'pengguna' => 'Dewi Anggraini', 'role' => 'Perencanaan',  'aktivitas' => 'Mengubah alokasi MAK', 'modul' => 'Mata Anggaran',   'hasil' => 'Gagal'],
            ],
        ];
    }
}
