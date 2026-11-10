@extends('layouts.ppbj')

@section('judul_halaman', 'Dashboard PPBJ')
@section('menu_aktif', 'dashboard')

@section('konten')

{{--
    Dashboard PPBJ (4 kartu ringkasan + tabel "Daftar Permohonan").
    Variabel dari Ppbj\DashboardController@index: $ringkasan, $baris, $total, $paginasi, $filter.

    REUSE: layout ini ada di komponen <x-ppbj.dashboard> dan dipakai IDENTIK oleh dashboard "Kontrak & SPK" (ppbj/kontrak/index.blade.php)
    dan "Pembayaran" (ppbj/pembayaran/index.blade.php). Untuk membuat dashboard baru cukup menukar:
      - $ringkasan  (jumlah kartu 3/4/5 mengikuti isi array),
      - $kolom      (judul dan tipe kolom tabel),
      - $pilStatus  (peta status => label + warna).
--}}

@php
    /* Kolom tabel Dashboard PPBJ: No Surat, Jenis Pengadaan, Judul Pengadaan, Status, PIC Pengadaan (+ Aksi otomatis) */
    $kolom = [
        ['label' => 'No Surat',         'tipe' => 'surat',  'kunci' => 'no_surat', 'lebar' => 'w-[290px]'],
        ['label' => 'Jenis Pengadaan',  'tipe' => 'jenis',  'kunci' => 'jenis',    'lebar' => 'w-[150px]'],
        ['label' => 'Judul Pengadaan',  'tipe' => 'teks',   'kunci' => 'judul'],
        ['label' => 'Status',           'tipe' => 'status', 'kunci' => 'status',   'lebar' => 'w-[125px]'],
        ['label' => 'PIC Pengadaan',    'tipe' => 'pic',    'kunci' => 'pic',      'lebar' => 'w-[185px]'],
    ];

    /* Peta status => [label, kelas warna]: DIPROSES kuning, REVISI ungu, SELESAI hijau, TINJAUAN abu-abu */
    $pilStatus = [
        'diproses' => ['DIPROSES', 'bg-[#fef3c7] text-[#f0a30a]'],
        'revisi'   => ['REVISI',   'bg-[#f4e8fc] text-[#9b30e0]'],
        'selesai'  => ['SELESAI',  'bg-[#d9f5e5] text-[#1fa35c]'],
        'tinjauan' => ['TINJAUAN', 'bg-[#f1f2f6] text-gray-500'],
    ];

    /* Peta jenis pengadaan => [label, kelas warna]: JASA ungu, BARANG biru */
    $pilJenis = [
        'jasa'   => ['JASA',   'bg-[#f3e8fc] text-[#9b30e0]'],
        'barang' => ['BARANG', 'bg-[#dff1fd] text-[#1c8fd6]'],
    ];
@endphp

{{-- Tombol "Detail" setiap baris menuju Metode Pengadaan (ppbj.pengadaan.show) --}}
<x-ppbj.dashboard :ringkasan="$ringkasan" judul-tabel="Daftar Permohonan" :kolom="$kolom" :baris="$baris"
                  :pil-status="$pilStatus" :pil-jenis="$pilJenis" :total="$total" :paginasi="$paginasi" :filter="$filter"
                  :url-halaman="route('ppbj.dashboard')" rute-detail="ppbj.pengadaan.show" />

@endsection
