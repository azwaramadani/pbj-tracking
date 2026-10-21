@extends('layouts.unit')

@section('judul_halaman', 'Detail Permohonan')
@section('menu_aktif', 'permohonan')

@section('konten')

@php
    /*
     * Variabel dari PermohonanController@show: $permohonan (array satu permohonan, lihat App\Support\Unit\DataPermohonan).
     * Warna lencana tahap mengikuti status permohonan.
     */
    $kelasLencana = [
        'diproses' => 'bg-[#eaf3fe] text-[#2f80ed]',
        'menunggu' => 'bg-[#fff6e6] text-[#d87705]',
        'selesai'  => 'bg-[#e9f6ee] text-[#1f9d5b]',
        'draft'    => 'bg-gray-100 text-gray-500',
    ][$permohonan['status']];

    /* Baris informasi pada kartu "Informasi Surat Permohonan": [label, isi] */
    $infoSurat = [
        ['Nomor Surat',        $permohonan['no_surat']],
        ['Judul Pengadaan',    $permohonan['judul']],
        ['Asal Unit Pengusul', $permohonan['unit']],
        ['Deskripsi & Urgensi', $permohonan['deskripsi']],
        ['Tanggal Pengajuan',  $permohonan['tgl_kirim_lengkap']],
    ];
@endphp

<!-- ===================== TAUTAN KEMBALI ===================== -->
<a href="{{ route('unit.permohonan.index') }}" class="mb-6 inline-flex h-6 items-center gap-2 text-[14px] font-semibold text-brand-800 transition hover:text-brand-700">
    <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
    </svg>
    Kembali ke Daftar Permohonan
</a>

<!-- ===================== BARIS ATAS: INFORMASI SURAT (kiri) + LAMPIRAN (kanan) ===================== -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-[3fr_2fr]">

    <!-- Kartu "Informasi Surat Permohonan" -->
    <section class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-[18px] font-bold">Informasi Surat Permohonan</h2>
            <!-- Lencana tahap permohonan saat ini -->
            <span class="inline-flex h-[22px] items-center rounded-full px-2.5 text-[12px] font-semibold {{ $kelasLencana }}">{{ $permohonan['tahap'] }}</span>
        </div>

        <!-- Daftar pasangan label (huruf kapital abu-abu) dan isi -->
        <dl class="space-y-3.5">
            @foreach ($infoSurat as [$label, $isi])
                <div>
                    <dt class="text-[13px] uppercase leading-4 text-gray-500">{{ $label }}</dt>
                    <dd class="mt-2 text-[14px] leading-[1.5]">{{ $isi }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    <!-- Kartu "Lampiran Dokumen": satu baris per berkas dengan tombol unduh -->
    <section class="rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="mb-5 text-[18px] font-bold">Lampiran Dokumen</h2>

        <ul class="space-y-3">
            @foreach ($permohonan['lampiran'] as $berkas)
                <li class="flex items-center gap-3 rounded-lg border border-gray-200 bg-surface px-3.5 py-2.5">
                    <!-- Ikon dokumen -->
                    <svg class="h-[18px] w-[18px] shrink-0 text-accent" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>

                    <!-- Nama berkas dan ukurannya -->
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-semibold leading-snug">{{ $berkas['nama'] }}</p>
                        <p class="text-[12px] leading-snug text-gray-500">{{ $berkas['ukuran'] }}</p>
                    </div>

                    <!-- Tombol unduh -->
                    <a href="#" aria-label="Unduh {{ $berkas['nama'] }}" class="shrink-0 text-brand-800 transition hover:text-brand-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
</div>

<!-- ===================== KARTU "LOG PENINJAUAN SINGKAT" ===================== -->
<section class="mt-6 rounded-xl border border-gray-200 bg-white p-6">
    <h2 class="mb-5 text-[18px] font-bold">Log Peninjauan Singkat</h2>

    <!-- Rangkaian langkah horizontal; antar langkah dipisahkan ikon panah kecil -->
    <ol class="flex flex-wrap items-center gap-y-4">
        @foreach ($permohonan['log'] as $langkah)
            @php $aktif = $langkah['state'] === 'aktif'; @endphp

            <li class="flex items-center gap-3">
                <!-- Ikon langkah: centang hijau untuk yang selesai, lingkaran biru berinti putih untuk yang sedang berjalan -->
                @if ($aktif)
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#2f80ed]">
                        <span class="h-2 w-2 rounded-full bg-white"></span>
                    </span>
                @else
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#26ae60] text-white">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="3.2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </span>
                @endif

                <!-- Judul langkah dan keterangannya; langkah aktif berwarna biru -->
                <div>
                    <p class="text-[14px] font-semibold leading-tight {{ $aktif ? 'text-[#2f80ed]' : '' }}">{{ $langkah['judul'] }}</p>
                    <p class="text-[12px] leading-tight text-gray-500">{{ $langkah['keterangan'] }}</p>
                </div>
            </li>

            <!-- Panah pemisah antar langkah (tidak ditampilkan setelah langkah terakhir) -->
            @unless ($loop->last)
                <li aria-hidden="true" class="mx-8 text-gray-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </li>
            @endunless
        @endforeach
    </ol>
</section>

@endsection
