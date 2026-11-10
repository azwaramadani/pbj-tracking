@extends('layouts.ppbj')

@section('judul_halaman', 'Dashboard Kontrak & SPK')
@section('menu_aktif', 'kontrak')

@section('konten')

{{--
    Dashboard Kontrak & SPK - LAYOUT SAMA PERSIS dengan Dashboard PPBJ (komponen <x-ppbj.dashboard>); hanya data kartu dan kolom yang ditukar.
    Variabel dari Ppbj\KontrakController@index: $ringkasan (3 kartu), $baris, $total, $paginasi, $filter.
--}}

@php
    /* Kolom tabel: No Surat, Nomor SPK (huruf kecil sesuai mockup), Judul Pengadaan, Status, Tanggal (+ Aksi otomatis) */
    $kolom = [
        ['label' => 'No Surat',        'tipe' => 'surat',   'kunci' => 'no_surat',  'lebar' => 'w-[285px]'],
        ['label' => 'Nomor SPK',       'tipe' => 'spk',     'kunci' => 'nomor_spk', 'lebar' => 'w-[215px]', 'huruf' => false],
        ['label' => 'Judul Pengadaan', 'tipe' => 'teks',    'kunci' => 'judul'],
        ['label' => 'Status',          'tipe' => 'status',  'kunci' => 'status',    'lebar' => 'w-[125px]'],
        ['label' => 'Tanggal',         'tipe' => 'tanggal', 'kunci' => 'tanggal',   'lebar' => 'w-[125px]'],
    ];

    /* Peta status: TINJAUAN abu-abu (perlu unggah), DIPROSES kuning, SELESAI hijau */
    $pilStatus = [
        'tinjauan' => ['TINJAUAN', 'bg-[#f1f2f6] text-gray-500'],
        'diproses' => ['DIPROSES', 'bg-[#fef3c7] text-[#f0a30a]'],
        'selesai'  => ['SELESAI',  'bg-[#d9f5e5] text-[#1fa35c]'],
    ];
@endphp

{{-- Judul tabel mengikuti mockup ("Daftar Penerimaan"). Tombol "Detail" menuju Detail Kontrak (ppbj.kontrak.show) --}}
<x-ppbj.dashboard :ringkasan="$ringkasan" judul-tabel="Daftar Penerimaan" :kolom="$kolom" :baris="$baris"
                  :pil-status="$pilStatus" :total="$total" :paginasi="$paginasi" :filter="$filter"
                  :url-halaman="route('ppbj.kontrak.index')" rute-detail="ppbj.kontrak.show" />

@endsection
