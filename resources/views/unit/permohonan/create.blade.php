@extends('layouts.unit')

@section('judul_halaman', 'Buat Permohonan')
@section('menu_aktif', 'permohonan')
@section('url_kembali', route('unit.dashboard'))

@section('konten')

<!-- Formulir kosong untuk membuat permohonan baru; isi formulir ada di partial _form (dipakai bersama halaman Revisi Pengajuan) -->
@include('unit.permohonan._form')

@endsection
