@extends('layouts.unit')

{{--
    Layout role Perencanaan. Memakai ulang kerangka layouts/unit (sidebar gradien, header, kotak tanggal),
    hanya menimpa menu sidebar, label modul, dan jejak halaman (breadcrumb).
    Halaman Perencanaan cukup @extends('layouts.perencanaan') lalu mengisi section judul_halaman, menu_aktif, dan konten.
--}}

@php
    /* Menu sidebar Perencanaan: tiga tautan (Dashboard, Notifikasi, Profil) */
    $menuSidebar = [
        ['kunci' => 'dashboard',  'label' => 'Dashboard',  'rute' => 'perencanaan.dashboard',
         'ikon'  => 'M3.75 15.75a8.25 8.25 0 1116.5 0M12 15.75l3-4.5'],
        ['kunci' => 'notifikasi', 'label' => 'Notifikasi', 'rute' => 'perencanaan.notifikasi.index', 'badge' => 3,
         'ikon'  => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'],
        ['kunci' => 'profil',     'label' => 'Profil',     'rute' => 'perencanaan.profil.show',
         'ikon'  => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
    ];

    $labelModul = 'Modul Perencanaan';
    $breadcrumb = 'SIMPATIK-PBJ / Perencanaan';

    // Pil "TA 2026 - AKTIF" berwarna hijau pada halaman Perencanaan
    $kelasPilTa = 'border-[#7fcfae] bg-[#e4f7ee] text-[#15965b]';
@endphp
