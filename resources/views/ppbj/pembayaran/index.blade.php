@extends('layouts.ppbj')

@section('judul_halaman', 'Dashboard Pembayaran')
@section('menu_aktif', 'pembayaran')

@section('konten')

{{--
    Dashboard Pembayaran - LAYOUT SAMA PERSIS dengan Dashboard PPBJ (komponen <x-ppbj.dashboard>); hanya data kartu (5 kartu) dan peta status yang ditukar.
    Variabel dari Ppbj\PembayaranController@index: $ringkasan, $baris, $total, $paginasi, $filter.
--}}

@php
    /* Kolom tabel sama dengan Dashboard Kontrak & SPK */
    $kolom = [
        ['label' => 'No Surat',        'tipe' => 'surat',   'kunci' => 'no_surat',  'lebar' => 'w-[285px]'],
        ['label' => 'Nomor SPK',       'tipe' => 'spk',     'kunci' => 'nomor_spk', 'lebar' => 'w-[215px]', 'huruf' => false],
        ['label' => 'Judul Pengadaan', 'tipe' => 'teks',    'kunci' => 'judul'],
        ['label' => 'Status',          'tipe' => 'status',  'kunci' => 'status',    'lebar' => 'w-[135px]'],
        ['label' => 'Tanggal',         'tipe' => 'tanggal', 'kunci' => 'tanggal',   'lebar' => 'w-[125px]'],
    ];

    /* Lima status pembayaran: TINJAUAN abu-abu, PERSETUJUAN tosca, DIPROSES kuning, PEMBAYARAN ungu, SELESAI hijau */
    $pilStatus = [
        'tinjauan'    => ['TINJAUAN',    'bg-[#f1f2f6] text-gray-500'],
        'persetujuan' => ['PERSETUJUAN', 'bg-[#d5f5f1] text-[#14a89a]'],
        'diproses'    => ['DIPROSES',    'bg-[#fef3c7] text-[#f0a30a]'],
        'pembayaran'  => ['PEMBAYARAN',  'bg-[#f3e8fc] text-[#9b30e0]'],
        'selesai'     => ['SELESAI',     'bg-[#d9f5e5] text-[#1fa35c]'],
    ];
@endphp

{{-- Judul tabel mengikuti mockup ("Daftar Penerimaan"). Tombol "Detail" menuju satu halaman yang berganti tampilan menurut status (ppbj.pembayaran.show) --}}
<x-ppbj.dashboard :ringkasan="$ringkasan" judul-tabel="Daftar Penerimaan" :kolom="$kolom" :baris="$baris"
                  :pil-status="$pilStatus" :total="$total" :paginasi="$paginasi" :filter="$filter"
                  :url-halaman="route('ppbj.pembayaran.index')" rute-detail="ppbj.pembayaran.show" />

@endsection
