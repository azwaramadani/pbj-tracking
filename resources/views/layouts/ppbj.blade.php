@extends('layouts.unit')

{{--
    Layout role PPBJ. Memakai ulang kerangka layouts/unit (sidebar gradien, header, kotak tanggal),
    hanya menimpa menu sidebar, label modul, dan jejak halaman (breadcrumb).
    Halaman PPBJ cukup @extends('layouts.ppbj') lalu mengisi section judul_halaman, menu_aktif, dan konten
    (opsional: url_kembali dan breadcrumb_ekstra untuk halaman detail).
--}}

@php
    /* Menu sidebar PPBJ: lima tautan (Dashboard, Kontrak & SPK, Pembayaran, Notifikasi, Profil) */
    $menuSidebar = [
        ['kunci' => 'dashboard',  'label' => 'Dashboard',     'rute' => 'ppbj.dashboard',
         'ikon'  => 'M3.75 15.75a8.25 8.25 0 1116.5 0M12 15.75l3-4.5'],
        ['kunci' => 'kontrak',    'label' => 'Kontrak & SPK', 'rute' => 'ppbj.kontrak.index',
         'ikon'  => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        ['kunci' => 'pembayaran', 'label' => 'Pembayaran',    'rute' => 'ppbj.pembayaran.index',
         'ikon'  => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z'],
        ['kunci' => 'notifikasi', 'label' => 'Notifikasi',    'rute' => 'ppbj.notifikasi.index', 'badge' => 3,
         'ikon'  => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'],
        ['kunci' => 'profil',     'label' => 'Profil',        'rute' => 'ppbj.profil.show',
         'ikon'  => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
    ];

    $labelModul = 'Modul PPBJ';
    $breadcrumb = 'SIMPATIK-PBJ / PPBJ';

    // Pil "TA 2026 - AKTIF" berwarna hijau pada halaman PPBJ
    $kelasPilTa = 'border-[#7fcfae] bg-[#e4f7ee] text-[#15965b]';
@endphp
