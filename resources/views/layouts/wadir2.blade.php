@extends('layouts.unit')

{{--
    Layout role Wadir 2. Memakai ulang kerangka layouts/unit (sidebar gradien, header, kotak tanggal),
    hanya menimpa menu sidebar, label modul, jejak halaman (breadcrumb), dan warna pil tahun anggaran.
    Halaman Wadir 2 cukup @extends('layouts.wadir2') lalu mengisi section judul_halaman, menu_aktif, dan konten.
--}}

@php
    /*
     * Menu sidebar Wadir 2: sama seperti User Unit, tetapi "Permohonan" diganti "Pembayaran".
     * Rute yang belum dibuat otomatis diarahkan ke "#" oleh layout dasar.
     */
    $menuSidebar = [
        ['kunci' => 'dashboard',  'label' => 'Dashboard',  'rute' => 'wadir2.dashboard',
         'ikon'  => 'M3.75 15.75a8.25 8.25 0 1116.5 0M12 15.75l3-4.5'],
        ['kunci' => 'pembayaran', 'label' => 'Pembayaran', 'rute' => 'wadir2.pembayaran.index',
         'ikon'  => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        ['kunci' => 'notifikasi', 'label' => 'Notifikasi', 'rute' => 'wadir2.notifikasi.index', 'badge' => 3,
         'ikon'  => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'],
        ['kunci' => 'profil',     'label' => 'Profil',     'rute' => 'wadir2.profil.show',
         'ikon'  => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
    ];

    $labelModul = 'Modul Wadir 2';
    $breadcrumb = 'SIMPATIK-PBJ / Wadir 2';

    // Pil "TA 2026 - AKTIF" berwarna hijau pada halaman Wadir 2; halaman yang memakai pil abu-abu (mis. Persetujuan Paket Pembayaran)
    // cukup mengisi section pil_ta_abu
    $kelasPilTa = trim($__env->yieldContent('pil_ta_abu')) !== ''
        ? 'border-[#aac2c7] bg-[#cad9de] text-brand-800'
        : 'border-[#7fcfae] bg-[#e4f7ee] text-[#15965b]';
@endphp
