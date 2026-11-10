@extends('layouts.penerimaan')

@section('judul_halaman', 'Notifikasi')
@section('menu_aktif', 'notifikasi')

{{-- Header Notifikasi digambar di dalam komponen, jadi header standar layout diganti komentar ini --}}
@section('header_halaman')<!-- Header halaman dirender oleh komponen notifikasi-panel -->@endsection

@section('konten')
<x-notifikasi-panel :notifications="$notifikasi" :tab-counts="$jumlahTab" :active-tab="$tabAktif" route-prefix="penerimaan.notifikasi" />
@endsection
