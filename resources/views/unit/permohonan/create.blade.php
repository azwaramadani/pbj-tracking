@extends('layouts.unit')

@section('judul_halaman', 'Buat Permohonan')
@section('menu_aktif', 'permohonan')
@section('url_kembali', route('unit.permohonan.index'))

@section('konten')

@if (session('success'))
    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        <p class="mb-2 font-semibold">Terdapat kesalahan pada formulir:</p>
        <ul class="list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@include('unit.permohonan._form')

@endsection