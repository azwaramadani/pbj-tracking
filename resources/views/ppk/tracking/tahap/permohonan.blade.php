@extends('layouts.ppk')

@section('judul_halaman', 'Detail Permohonan')
@section('menu_aktif', $menuAktif)
@section('url_kembali', $urlKembali)

@section('konten')

{{--
    Tahap 1-3 Alur Perjalanan Pengadaan (Permohonan Diajukan, Persetujuan Wadir 2 dan PPK, Perencanaan/MAK):
    sama dengan Detail Permohonan User Unit, jadi cukup memakai komponen bersama x-unit.ringkasan-permohonan.
    Variabel dari Ppk\TrackingController@tahap: $baris, $lampiran, $kartu - lihat App\Support\Ppk\DataTahap::permohonan()
--}}
<x-unit.ringkasan-permohonan :baris="$baris" :lampiran="$lampiran" :kartu="$kartu" />

@endsection
