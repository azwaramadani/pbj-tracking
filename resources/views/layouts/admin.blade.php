@extends('layouts.unit')

{{--
    Layout role Super Admin (badge "ADMIN PANEL"). Memakai ulang kerangka layouts/unit, hanya menimpa menu sidebar
    (dikelompokkan: RINGKASAN, MASTER DATA, TRANSAKSI, SISTEM), label modul, breadcrumb, pil "ADMIN", dan kartu pengguna.
    Halaman Super Admin cukup @extends('layouts.admin') lalu mengisi section judul_halaman, deskripsi_halaman (opsional),
    menu_aktif, dan konten (opsional: url_kembali untuk halaman detail).
--}}

@php
    /*
     * Menu sidebar Super Admin. Item ['grup' => ...] hanya judul kelompok; 'kunci' dicocokkan dengan section menu_aktif.
     * TODO Backend: "Logout" sementara menuju halaman login; ganti dengan form POST ke route logout (@csrf) saat autentikasi siap.
     */
    $menuSidebar = [
        ['grup' => 'RINGKASAN'],
        ['kunci' => 'dashboard',  'label' => 'Dashboard',            'rute' => 'admin.dashboard',
         'ikon'  => 'M3.75 15.75a8.25 8.25 0 1116.5 0M12 15.75l3-4.5'],

        ['grup' => 'MASTER DATA'],
        ['kunci' => 'pengguna',   'label' => 'Manajemen Pengguna',   'rute' => 'admin.pengguna.index',
         'ikon'  => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
        ['kunci' => 'role',       'label' => 'Role & Hak Akses',     'rute' => 'admin.role.index',
         'ikon'  => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z'],
        ['kunci' => 'unit',       'label' => 'Unit / Fakultas',      'rute' => 'admin.unit.index',
         'ikon'  => 'M2.25 21h19.5M3.75 3h16.5M4.5 3v18m15-18v18M9 7.5h1.5M9 11.25h1.5M13.5 7.5H15m-1.5 3.75H15M10.5 21v-3.75h3V21'],
        ['kunci' => 'mak',        'label' => 'Mata Anggaran (MAK)',  'rute' => 'admin.mak.index',
         'ikon'  => 'M3 5.25h18M3 9.75h18M3 14.25h18M3 18.75h18M9 5.25v13.5'],

        ['grup' => 'TRANSAKSI'],
        ['kunci' => 'pengajuan',  'label' => 'Pengajuan Surat',      'rute' => 'admin.pengajuan.index',
         'ikon'  => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        ['kunci' => 'pengadaan',  'label' => 'Data Pengadaan',       'rute' => 'admin.pengadaan.index',
         'ikon'  => 'M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9'],
        ['kunci' => 'dokumen',    'label' => 'Dokumen & Lampiran',   'rute' => 'admin.dokumen.index',
         'ikon'  => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        ['kunci' => 'approval',   'label' => 'Riwayat Approval',     'rute' => 'admin.approval.index',
         'ikon'  => 'M4.5 12.75l6 6 9-13.5'],
        ['kunci' => 'reminder',   'label' => 'Reminder',             'rute' => 'admin.reminder.index',
         'ikon'  => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['kunci' => 'penerimaan', 'label' => 'Penerimaan Barang',    'rute' => 'admin.penerimaan.index',
         'ikon'  => 'M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z'],
        ['kunci' => 'pembayaran', 'label' => 'Pembayaran',           'rute' => 'admin.pembayaran.index',
         'ikon'  => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],

        ['grup' => 'SISTEM'],
        ['kunci' => 'notifikasi', 'label' => 'Notifikasi',           'rute' => 'admin.notifikasi.index',
         'ikon'  => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'],
        ['kunci' => 'audit',      'label' => 'Audit Log / Riwayat',  'rute' => 'admin.audit.index',
         'ikon'  => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['kunci' => 'logout',     'label' => 'Logout',               'rute' => 'login',
         'ikon'  => 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9'],
    ];

    $labelModul = 'Admin Panel';
    $breadcrumb = 'SIMPATIK-PBJ / Admin';

    // Pil di samping judul bertuliskan "ADMIN" (hijau), menggantikan "TA 2026 - AKTIF"
    $teksPilTa  = 'ADMIN';
    $kelasPilTa = 'border-[#7fcfae] bg-[#e4f7ee] text-[#15965b] !px-[26px] !text-[11px]';

    // Menu panjang (15 item + 4 judul kelompok): item dipadatkan agar muat di layar
    $tinggiMenu = 'h-[36px]';
    $jarakMenu  = 'space-y-[2px]';

    // TODO Backend: ganti dengan pengguna Super Admin yang sedang login (auth()->user())
    $penggunaLogin = ['nama' => 'Administrator', 'nip' => '', 'inisial' => 'SA', 'keterangan' => 'Super Admin · Sistem'];
@endphp
