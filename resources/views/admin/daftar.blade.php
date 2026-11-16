
{{--
    View generik halaman daftar Super Admin.
    Khusus Manajemen Pengguna menggunakan data PostgreSQL.
    Halaman admin lainnya tetap menggunakan DataAdmin.
--}}

@extends('layouts.admin')

@section('judul_halaman', $pageTitle)
@section('deskripsi_halaman', $pageDescription)
@section('menu_aktif', $menuAktif)

@section('konten')

@php
    $kelasPil = [
        'hijau'  => 'bg-[#e4f7ee] text-[#15965b]',
        'kuning' => 'bg-[#fff3d6] text-[#d98a00]',
        'merah'  => 'bg-[#fde8e8] text-[#d93a3a]',
        'biru'   => 'bg-[#e6f0ff] text-[#2f80ed]',
        'abu'    => 'bg-[#f1f2f6] text-gray-500',
    ];
@endphp

<div class="xl:pr-4">

    {{-- GRID KARTU RINGKASAN --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        @foreach ($summaryCards as $kartu)
            <div class="relative grid h-[80px] items-center overflow-hidden rounded-xl border border-gray-100 bg-white px-[15px] shadow-[0_1px_3px_rgba(15,23,42,0.06)]">

                <div>
                    <p class="text-[28px] font-bold leading-8">
                        {{ $kartu['jumlah'] }}
                    </p>

                    <p class="mt-1 text-[12px] font-semibold leading-4 text-gray-700">
                        {{ $kartu['label'] }}
                    </p>
                </div>

                <svg
                    class="pointer-events-none absolute bottom-0 right-0 h-[44px] w-[118px]"
                    viewBox="0 0 118 44"
                    fill="none"
                    aria-hidden="true"
                >
                    <path
                        d="M0 44 C40 42 72 35 118 0 L118 44 Z"
                        fill="#57818d"
                    />
                </svg>

            </div>
        @endforeach

    </section>

    {{-- KARTU TABEL --}}
    <section class="mt-4 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-[0_1px_3px_rgba(15,23,42,0.06)]">

        {{-- JUDUL DAN AKSI --}}
        <div class="flex flex-wrap items-center justify-between gap-3 p-5">

            <div class="flex items-center gap-5">

                <h2 class="text-[20px] font-bold leading-7">
                    Daftar {{ $pageTitle }}
                </h2>

                <span class="rounded-md border border-[#cdd9e3] bg-[#ebf3f9] px-[14px] py-[3px] text-[14px] text-gray-600">
                    Total: {{ $totalData }} data
                </span>

            </div>

            {{-- AREA PENCARIAN, FILTER, DAN TAMBAH --}}
            <div class="flex flex-wrap items-center gap-[12px]">

                {{-- FORM PENCARIAN DAN FILTER --}}
                <form
                    method="GET"
                    action="{{ request()->url() }}"
                    class="flex flex-wrap items-center gap-[12px]"
                >

                    {{-- PENCARIAN --}}
                    <div class="relative">

                        <svg
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-600"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                            />
                        </svg>

                        <input
                            type="text"
                            name="q"
                            value="{{ $filter['q'] }}"
                            placeholder="Cari data..."
                            class="h-[34px] w-[255px] rounded-lg border border-gray-200 bg-white pl-8 pr-2 text-[14px] text-gray-900 placeholder-gray-500 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20"
                        >

                    </div>

                    {{-- FILTER --}}
                    <details class="relative">

                        <summary class="flex h-[34px] cursor-pointer list-none items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-[12px] text-[14px] font-semibold transition hover:bg-gray-50 [&::-webkit-details-marker]:hidden">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"
                                />
                            </svg>

                            Filter

                        </summary>

                        <div class="absolute right-0 z-20 mt-2 w-[220px] rounded-lg border border-gray-200 bg-white p-3 shadow-lg">

                            <label
                                for="filter-status"
                                class="mb-1.5 block text-[12px] font-semibold text-gray-600"
                            >
                                {{ $filterLabel }}
                            </label>

                            <select
                                id="filter-status"
                                name="status"
                                class="h-9 w-full rounded-md border border-gray-200 bg-white px-2 text-[13px] outline-none focus:border-tosca-600"
                            >
                                <option value="">Semua</option>

                                @foreach ($filterOpsi as $opsi)
                                    <option
                                        value="{{ $opsi }}"
                                        @selected($filter['status'] === $opsi)
                                    >
                                        {{ $opsi }}
                                    </option>
                                @endforeach

                            </select>

                            <button
                                type="submit"
                                class="mt-3 h-9 w-full rounded-md bg-tosca-900 text-[13px] font-semibold text-white transition hover:bg-tosca-800"
                            >
                                Terapkan
                            </button>

                        </div>

                    </details>

                </form>

                {{-- TOMBOL TAMBAH --}}
                @if ($tampilTambah)

                    @if ($menuAktif === 'pengguna')

                        <a
                            href="{{ route('admin.pengguna.create') }}"
                            class="flex h-[34px] items-center gap-1.5 rounded-lg bg-tosca-900 px-[18px] text-[14px] font-semibold text-white transition hover:bg-tosca-800"
                        >
                            <span class="text-[16px] leading-none">+</span>
                            Tambah
                        </a>

                    @else

                        <button
                            type="button"
                            class="flex h-[34px] items-center gap-1.5 rounded-lg bg-tosca-900 px-[18px] text-[14px] font-semibold text-white transition hover:bg-tosca-800"
                        >
                            <span class="text-[16px] leading-none">+</span>
                            Tambah
                        </button>

                    @endif

                @endif

            </div>

        </div>

        {{-- TABEL DATA --}}
        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px] table-fixed text-left">

                <colgroup>

                    @foreach ($tableHeaders as $k)
                        <col>
                    @endforeach

                    <col class="w-[95px]">

                </colgroup>

                {{-- HEADER --}}
                <thead class="bg-tosca-900 text-[12px] font-semibold text-white">

                    <tr class="h-[44px]">

                        @foreach ($tableHeaders as $k)
                            <th class="{{ $loop->first ? 'pl-5' : '' }} font-semibold uppercase">
                                {{ $k['label'] }}
                            </th>
                        @endforeach

                        <th class="font-semibold uppercase">
                            Aksi
                        </th>

                    </tr>

                </thead>

                {{-- BODY --}}
                <tbody class="text-[13px]">

                    @foreach ($rows as $b)

                        <tr class="h-[57px] border-b border-gray-100">

                            @foreach ($tableHeaders as $k)

                                @php
                                    $nilai = $b[$k['kunci']] ?? '';
                                @endphp

                                @switch($k['tipe'])

                                    @case('utama')

                                        <td class="pl-5 pr-3">

                                            <p class="truncate font-bold leading-4">
                                                {{ $nilai }}
                                            </p>

                                            <p class="mt-0.5 text-[11px] leading-4 text-gray-400">
                                                {{ $b['keterangan'] ?? 'Data contoh' }}
                                            </p>

                                        </td>

                                        @break

                                    @case('pil')

                                        @php
                                            $warna = $k['pil'][$nilai] ?? null;
                                        @endphp

                                        <td class="pr-3">

                                            @if ($warna)

                                                <span class="inline-flex h-[22px] items-center justify-center rounded-lg px-4 text-[11px] font-bold {{ ($k['huruf'] ?? true) ? 'uppercase' : '' }} {{ $kelasPil[$warna] ?? $kelasPil['abu'] }}">
                                                    {{ $nilai }}
                                                </span>

                                            @else

                                                <span class="uppercase text-gray-800">
                                                    {{ $nilai }}
                                                </span>

                                            @endif

                                        </td>

                                        @break

                                    @default

                                        <td
                                            class="truncate pr-3 text-gray-800"
                                            title="{{ $nilai }}"
                                        >
                                            {{ $nilai }}
                                        </td>

                                @endswitch

                            @endforeach

                            {{-- KOLOM AKSI --}}
                            <td>

                                @if ($ruteDetail)

                                    <a
                                        href="{{ route($ruteDetail, $b['id']) }}"
                                        class="inline-flex h-[28px] w-[49px] items-center justify-center rounded-md bg-tosca-900 text-[11px] font-semibold text-white transition hover:bg-tosca-800"
                                    >
                                        Detail
                                    </a>

                                @else

                                    <button
                                        type="button"
                                        data-buka-detail-admin
                                        data-detail="{{ json_encode($b, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
                                        @if ($menuAktif === 'pengguna')
                                            data-update-url="{{ route('admin.pengguna.update', $b['id']) }}"
                                        @endif
                                        class="inline-flex h-[28px] w-[49px] items-center justify-center rounded-md bg-tosca-900 text-[11px] font-semibold text-white transition hover:bg-tosca-800"
                                    >
                                        Detail
                                    </button>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                    {{-- DATA KOSONG --}}
                    @if ($rows->isEmpty())

                        <tr class="h-[57px] border-b border-gray-100">

                            <td
                                colspan="{{ count($tableHeaders) + 1 }}"
                                class="px-5 text-center text-gray-500"
                            >
                                Tidak ada data yang sesuai.
                            </td>

                        </tr>

                    @endif

                </tbody>

            </table>

        </div>

        {{-- PAGINATION --}}
        <x-pagination :paginasi="$paginasi" />

    </section>

</div>

{{-- MODAL DETAIL / EDIT --}}
@if ($detail && ! $ruteDetail)

    <x-admin.modal-detail
        :objek="$detail['objek']"
        :judul="$detail['judul']"
        :fields="$detail['fields']"
    />

@endif

@endsection
