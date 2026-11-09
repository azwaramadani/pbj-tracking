@extends('layouts.unit')

{{--
    Layout role PPK (Pejabat Pembuat Komitmen). Memakai ulang kerangka layouts/unit (sidebar gradien, header, kotak tanggal),
    hanya menimpa menu sidebar, label modul, dan jejak halaman (breadcrumb).
    Halaman PPK cukup @extends('layouts.ppk') lalu mengisi section judul_halaman, menu_aktif, dan konten.
--}}

@php
    /* Menu sidebar PPK: tujuh tautan. Rute yang belum dibuat (mis. Pembayaran) otomatis diarahkan ke "#" oleh layout dasar */
    $menuSidebar = [
        ['kunci' => 'dashboard',      'label' => 'Dashboard',      'rute' => 'ppk.dashboard',
         'ikon'  => 'M3.75 15.75a8.25 8.25 0 1116.5 0M12 15.75l3-4.5'],
        ['kunci' => 'permohonan-mak', 'label' => 'Permohonan MAK', 'rute' => 'ppk.mak.index',
         'ikon'  => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        ['kunci' => 'pengadaan',      'label' => 'Pengadaan',      'rute' => 'ppk.pengadaan.index',
         'ikon'  => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
        ['kunci' => 'pembayaran',     'label' => 'Pembayaran',     'rute' => 'ppk.pembayaran.index',
         'ikon'  => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        ['kunci' => 'tracking',       'label' => 'Tracking',       'rute' => 'ppk.tracking.index',
         'ikon'  => 'M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z'],
        ['kunci' => 'notifikasi',     'label' => 'Notifikasi',     'rute' => 'ppk.notifikasi.index', 'badge' => 3,
         'ikon'  => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'],
        ['kunci' => 'profil',         'label' => 'Profil',         'rute' => 'ppk.profil.show',
         'ikon'  => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
    ];

    $labelModul = 'Modul PPK';
    $breadcrumb = 'SIMPATIK-PBJ / PPK';

    // Pil "TA 2026 - AKTIF" berwarna hijau pada halaman PPK
    $kelasPilTa = 'border-[#7fcfae] bg-[#e4f7ee] text-[#15965b]';
@endphp
