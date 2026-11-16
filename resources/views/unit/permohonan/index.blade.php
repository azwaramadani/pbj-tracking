
@extends('layouts.unit')

@section('judul_halaman', 'Daftar Permohonan')
@section('menu_aktif', 'permohonan')

@section('konten')

@php
    $tabStatus = [
        'semua' => 'Semua',
        'draft' => 'Draft',
        'menunggu' => 'Menunggu',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
    ];

    $warnaStatus = [
        'Draft' => 'bg-gray-100 text-gray-700',
        'Menunggu Persetujuan Awal' => 'bg-yellow-100 text-yellow-700',
        'Diproses' => 'bg-blue-100 text-blue-700',
        'Selesai' => 'bg-green-100 text-green-700',
        'Ditolak' => 'bg-red-100 text-red-700',
    ];
@endphp

<div class="space-y-6">

    {{-- Header halaman --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Daftar Permohonan
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola dan pantau seluruh permohonan pengadaan yang telah Anda buat.
            </p>
        </div>

        <a href="{{ route('unit.permohonan.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-800">
            <span>+</span>
            Buat Permohonan
        </a>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Ringkasan jumlah permohonan --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <p class="text-sm text-gray-500">Total Permohonan</p>
        <p class="mt-2 text-3xl font-bold text-gray-800">
            {{ $jumlahTab['semua'] ?? 0 }}
        </p>
        <p class="mt-1 text-xs text-gray-400">
            Seluruh permohonan yang Anda buat
        </p>
    </div>

    {{-- Tab status --}}
    <div class="overflow-x-auto">
        <div class="flex min-w-max gap-2 border-b border-gray-200">
            @foreach ($tabStatus as $key => $label)
                <a href="{{ route('unit.permohonan.index', array_filter([
                    'status' => $key,
                    'q' => $kataKunci ?: null,
                ])) }}"
                   class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-medium transition
                   {{ $statusAktif === $key
                        ? 'border-blue-700 text-blue-700'
                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">

                    {{ $label }}

                    <span class="rounded-full px-2 py-0.5 text-xs
                        {{ $statusAktif === $key
                            ? 'bg-blue-100 text-blue-700'
                            : 'bg-gray-100 text-gray-600' }}">
                        {{ $jumlahTab[$key] ?? 0 }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Pencarian --}}
    <form action="{{ route('unit.permohonan.index') }}"
          method="GET"
          class="flex flex-col gap-3 sm:flex-row">

        <input type="hidden" name="status" value="{{ $statusAktif }}">

        <div class="relative flex-1">
            <input
                type="text"
                name="q"
                value="{{ $kataKunci }}"
                placeholder="Cari nomor surat atau judul pengadaan..."
                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <button type="submit"
                class="rounded-xl bg-gray-800 px-6 py-3 text-sm font-semibold text-white transition hover:bg-gray-900">
            Cari
        </button>

        @if ($kataKunci !== '')
            <a href="{{ route('unit.permohonan.index', ['status' => $statusAktif]) }}"
               class="rounded-xl border border-gray-300 px-6 py-3 text-center text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                Reset
            </a>
        @endif
    </form>

    {{-- Informasi hasil --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                Permohonan Saya
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Menampilkan {{ $permohonan->count() }} dari
                {{ $permohonan->total() }} permohonan
            </p>
        </div>
    </div>

   
{{-- Daftar permohonan --}}
<div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

    @forelse ($permohonan as $item)

        @php
            $statusLabel = $item->Status_surat;
            $statusClass = $warnaStatus[$statusLabel] ?? 'bg-gray-100 text-gray-700';
        @endphp

        <a href="{{ $statusLabel === 'Draft'
            ? route('unit.permohonan.revisi', $item->Id_pengajuan)
            : route('unit.permohonan.show', $item->Id_pengajuan) }}"
           class="flex flex-col rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">

            {{-- Nomor surat dan status --}}
            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Nomor Surat
                    </p>

                    <p class="mt-1 break-all text-sm font-semibold text-gray-800">
                        {{ $item->No_surat }}
                    </p>
                </div>

                <span class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                    {{ $statusLabel }}
                </span>

            </div>

            <div class="my-4 border-t border-gray-100"></div>

            {{-- Informasi pengadaan --}}
            <div class="flex-1 space-y-4">

                <div>
                    <p class="text-xs text-gray-400">
                        Judul Pengadaan
                    </p>

                    <h3 class="mt-1 break-words text-base font-semibold leading-relaxed text-gray-800">
                        {{ $item->Judul_surat }}
                    </h3>
                </div>

                <div>
                    <p class="text-xs text-gray-400">
                        Asal Unit
                    </p>

                    <p class="mt-1 text-sm font-medium text-gray-700">
                        {{ $item->Asal_surat }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">
                        Nominal Pengadaan
                    </p>

                    <p class="mt-1 text-sm font-semibold text-gray-800">
                        @if ($item->Nominal_pengadaan)
                            Rp{{ number_format(
                                (int) preg_replace('/\D+/', '', (string) $item->Nominal_pengadaan),
                                0,
                                ',',
                                '.'
                            ) }}
                        @else
                            <span class="font-normal text-gray-400">
                                Belum diisi
                            </span>
                        @endif
                    </p>
                </div>

            </div>

            {{-- Tanggal dan ID --}}
            <div class="mt-5 border-t border-gray-100 pt-4">

                <div class="flex items-center justify-between gap-2">

                    <div>
                        <p class="text-xs text-gray-400">
                            Tanggal Pengajuan
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-700">
                            {{ $item->Tanggal_surat
                                ? $item->Tanggal_surat->format('d M Y')
                                : '-' }}
                        </p>
                    </div>

                    <span class="text-xs text-gray-400">
                        ID #{{ $item->Id_pengajuan }}
                    </span>

                </div>

                <p class="mt-3 text-xs font-medium text-blue-700">
                    {{ $statusLabel === 'Draft'
                        ? 'Klik untuk edit draft →'
                        : 'Klik untuk melihat detail →' }}
                </p>

            </div>

        </a>

    @empty

        <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-14 text-center">

            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-7 w-7 text-gray-400"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="1.5"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 8.414V19a2 2 0 01-2 2z" />
                </svg>
            </div>

            <h3 class="mt-4 text-lg font-semibold text-gray-800">
                Belum Ada Permohonan
            </h3>

            <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
                @if ($kataKunci !== '')
                    Tidak ditemukan permohonan yang sesuai dengan kata kunci pencarian.
                @elseif ($statusAktif !== 'semua')
                    Belum ada permohonan dengan status ini.
                @else
                    Anda belum membuat permohonan pengadaan. Silakan buat permohonan pertama Anda.
                @endif
            </p>

            @if ($kataKunci !== '')
                <a href="{{ route('unit.permohonan.index', ['status' => $statusAktif]) }}"
                   class="mt-5 inline-flex rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Hapus Pencarian
                </a>
            @elseif ($statusAktif === 'semua')
                <a href="{{ route('unit.permohonan.create') }}"
                   class="mt-5 inline-flex rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">
                    Buat Permohonan
                </a>
            @endif

        </div>

    @endforelse

</div>

    {{-- Pagination --}}
    @if ($permohonan->hasPages())
        <div class="flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:items-center sm:justify-between">

            <p class="text-sm text-gray-500">
                Menampilkan
                {{ $permohonan->firstItem() }}–{{ $permohonan->lastItem() }}
                dari {{ $permohonan->total() }} data
            </p>

            <div class="flex items-center gap-2">
                @if ($permohonan->onFirstPage())
                    <span class="cursor-not-allowed rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-300">
                        Sebelumnya
                    </span>
                @else
                    <a href="{{ $permohonan->previousPageUrl() }}"
                       class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Sebelumnya
                    </a>
                @endif

                @if ($permohonan->hasMorePages())
                    <a href="{{ $permohonan->nextPageUrl() }}"
                       class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        Berikutnya
                    </a>
                @else
                    <span class="cursor-not-allowed rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-300">
                        Berikutnya
                    </span>
                @endif
            </div>
        </div>
    @endif

</div>

@endsection
