@extends('layouts.unit')

@section('judul_halaman', 'Form Permohonan')
@section('menu_aktif', 'permohonan')
@section('url_kembali', route('unit.permohonan.show', $permohonan['id']))

@section('konten')

{{--
    Variabel dari Unit\PermohonanController@revisi:
      $permohonan : permohonan yang direvisi (id, no_surat, judul, nilai_anggaran, deskripsi, permintaan_perbaikan[])
    Kunci permintaan_perbaikan: diminta_oleh, peninjau, waktu, penanggung_jawab, catatan
--}}

@php
    /* Baris ringkasan "Permintaan Perbaikan": [label, isi]. TODO Backend: isi dari catatan revisi terakhir milik permohonan */
    $perbaikan = $permohonan['permintaan_perbaikan'];
    $barisPerbaikan = [
        ['Diminta oleh',       $perbaikan['diminta_oleh']],
        ['Peninjau',           $perbaikan['peninjau']],
        ['Tanggal & Waktu',    $perbaikan['waktu']],
        ['Penanggung Jawab',   $perbaikan['penanggung_jawab']],
        ['Catatan Revisi',     $perbaikan['catatan']],
    ];
@endphp

<!-- ===================== KOTAK PERMINTAAN PERBAIKAN (catatan dari peninjau) ===================== -->
<section class="rounded-2xl border border-gray-100 bg-white px-6 pb-[22px] pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
    <h2 class="text-[16px] font-bold leading-6">Permintaan Perbaikan</h2>

    <!-- Daftar label-isi: label abu-abu di kiri (lebar tetap), isi di kanan -->
    <dl class="mt-[11px] space-y-[14px]">
        @foreach ($barisPerbaikan as [$label, $isi])
            <div class="grid grid-cols-[180px_1fr] items-center">
                <dt class="text-[13px] leading-5 text-gray-500">{{ $label }}</dt>
                <dd class="text-[14px] font-medium leading-5 text-gray-900">{{ $isi }}</dd>
            </div>
        @endforeach
    </dl>
</section>

<!-- ===================== FORMULIR (terisi data permohonan yang akan direvisi) ===================== -->
<div class="mt-[25px]">
    @include('unit.permohonan._form')
</div>

@endsection
