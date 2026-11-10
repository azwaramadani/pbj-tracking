{{--
    View generik SEMUA halaman daftar Super Admin: Dashboard dan 13 halaman (Manajemen Pengguna, Role & Hak Akses, Unit/Fakultas,
    Mata Anggaran, Pengajuan Surat, Data Pengadaan, Dokumen & Lampiran, Riwayat Approval, Reminder, Penerimaan Barang,
    Pembayaran, Notifikasi, Audit Log). Struktur UI-nya sama: judul, deskripsi, 4 kartu ringkasan, bilah cari/filter,
    tombol "+ Tambah" (opsional), dan tabel. Cukup ganti variabel di bawah - HTML tidak perlu ditulis ulang per halaman.

    TODO Backend: isi variabel ini secara dinamis berdasarkan file migrasi tiap sub-modul. Contoh: $tableHeaders dibuat dari kolom
    tabel hasil migrasi (users, unit, mata_anggaran, dst.), $summaryCards dari query agregat (COUNT/SUM), dan $rows dari Eloquent
    ->paginate(). Saat ini seluruhnya berasal dari App\Support\Admin\DataAdmin lewat Admin\DaftarController / DashboardController.

    Variabel:
      $pageTitle        : judul halaman (juga dipakai pada judul kartu tabel, mis. "Daftar Manajemen Pengguna")
      $pageDescription  : kalimat deskripsi di bawah judul
      $summaryCards     : 4 kartu, tiap item ['jumlah' => angka/teks, 'label' => string]
      $tableHeaders     : kolom tabel, tiap item ['label', 'tipe' => utama|teks|pil, 'kunci' => kunci data, 'pil' => peta nilai => warna]
      $rows             : baris halaman ini (Collection array); $totalData, $paginasi, $filter, $filterOpsi, $filterLabel untuk cari/filter/halaman
      $tampilTambah     : true menampilkan tombol "+ Tambah" (halaman master saja)
      $ruteDetail       : nama route tombol Detail (Dashboard -> Detail Tracking); null = tombol Detail membuka modal detail halaman ini
      $detail           : konfigurasi modal Detail (objek, judul, fields) atau null
--}}
@extends('layouts.admin')

@section('judul_halaman', $pageTitle)
@section('deskripsi_halaman', $pageDescription)
@section('menu_aktif', $menuAktif)

@section('konten')

@php
    // Warna pil status (nama warna dikirim backend lewat peta 'pil' pada $tableHeaders)
    $kelasPil = [
        'hijau'  => 'bg-[#e4f7ee] text-[#15965b]',
        'kuning' => 'bg-[#fff3d6] text-[#d98a00]',
        'merah'  => 'bg-[#fde8e8] text-[#d93a3a]',
        'biru'   => 'bg-[#e6f0ff] text-[#2f80ed]',
        'abu'    => 'bg-[#f1f2f6] text-gray-500',
    ];
@endphp

<div class="xl:pr-4">

<!-- ===================== GRID 4 KARTU RINGKASAN (angka besar + label + gelombang tosca) ===================== -->
<section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <!-- MULAI LOOP: Kartu ringkasan (satu iterasi = satu kartu). TODO Backend: isi dari query agregat -->
    @foreach ($summaryCards as $kartu)
        <div class="relative grid h-[80px] items-center overflow-hidden rounded-xl border border-gray-100 bg-white px-[15px] shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <div>
                <p class="text-[28px] font-bold leading-8">{{ $kartu['jumlah'] }}</p>
                <p class="mt-1 text-[12px] font-semibold leading-4 text-gray-700">{{ $kartu['label'] }}</p>
            </div>
            <!-- Hiasan gelombang tosca di pojok kanan bawah kartu (murni dekorasi) -->
            <svg class="pointer-events-none absolute bottom-0 right-0 h-[44px] w-[118px]" viewBox="0 0 118 44" fill="none" aria-hidden="true">
                <path d="M0 44 C40 42 72 35 118 0 L118 44 Z" fill="#57818d"/>
            </svg>
        </div>
    @endforeach
    <!-- AKHIR LOOP: Kartu ringkasan -->
</section>

<!-- ===================== KARTU TABEL ===================== -->
<section class="mt-4 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-[0_1px_3px_rgba(15,23,42,0.06)]">

    <!-- Baris judul kartu: judul + pil total di kiri; pencarian, filter, dan tombol aksi tambah data di kanan -->
    <div class="flex flex-wrap items-center justify-between gap-3 p-5">
        <div class="flex items-center gap-5">
            <h2 class="text-[20px] font-bold leading-7">Daftar {{ $pageTitle }}</h2>
            <span class="rounded-md border border-[#cdd9e3] bg-[#ebf3f9] px-[14px] py-[3px] text-[14px] text-gray-600">Total: {{ $totalData }} data</span>
        </div>

        {{-- Pencarian + filter memakai method GET agar bisa di-bookmark; backend membaca parameter "q" (cari semua kolom) dan "status" (filter) --}}
        <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-[12px]">
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input type="text" name="q" value="{{ $filter['q'] }}" placeholder="Cari data..."
                       class="h-[34px] w-[255px] rounded-lg border border-gray-200 bg-white pl-8 pr-2 text-[14px] text-gray-900 placeholder-gray-500 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20">
            </div>

            <!-- Tombol Filter: membuka panel kecil berisi pilihan nilai kolom filter (elemen details, tanpa JavaScript) -->
            <details class="relative">
                <summary class="flex h-[34px] cursor-pointer list-none items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-[12px] text-[14px] font-semibold transition hover:bg-gray-50 [&::-webkit-details-marker]:hidden">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                    </svg>
                    Filter
                </summary>
                <div class="absolute right-0 z-20 mt-2 w-[220px] rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                    <label for="filter-status" class="mb-1.5 block text-[12px] font-semibold text-gray-600">{{ $filterLabel }}</label>
                    <select id="filter-status" name="status" class="h-9 w-full rounded-md border border-gray-200 bg-white px-2 text-[13px] outline-none focus:border-tosca-600">
                        <option value="">Semua</option>
                        @foreach ($filterOpsi as $opsi)
                            <option value="{{ $opsi }}" @selected($filter['status'] === $opsi)>{{ $opsi }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="mt-3 h-9 w-full rounded-md bg-tosca-900 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Terapkan</button>
                </div>
            </details>

            {{-- TODO Backend: arahkan tombol Tambah ke form tambah data (route create) milik sub-modul ini --}}
            @if ($tampilTambah)
                <button type="button" class="flex h-[34px] items-center gap-1.5 rounded-lg bg-tosca-900 px-[18px] text-[14px] font-semibold text-white transition hover:bg-tosca-800">
                    <span class="text-[16px] leading-none">+</span> Tambah
                </button>
            @endif
        </form>
    </div>

    <!-- Tabel (dibungkus agar bisa di-scroll horizontal di layar sempit) -->
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] table-fixed text-left">
            <colgroup>
                @foreach ($tableHeaders as $k)
                    <col>
                @endforeach
                <col class="w-[95px]">
            </colgroup>

            <!-- Kepala tabel: latar tosca gelap, teks putih kapital (judul kolom dikirim backend lewat $tableHeaders) -->
            <thead class="bg-tosca-900 text-[12px] font-semibold text-white">
                <tr class="h-[44px]">
                    @foreach ($tableHeaders as $k)
                        <th class="{{ $loop->first ? 'pl-5' : '' }} font-semibold uppercase">{{ $k['label'] }}</th>
                    @endforeach
                    <th class="font-semibold uppercase">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-[13px]">
                <!-- MULAI LOOP: Baris data dari database (satu iterasi = satu baris tabel). TODO Backend: ganti $rows dengan hasil query Eloquent -->
                @foreach ($rows as $b)
                    <tr class="h-[57px] border-b border-gray-100">
                        @foreach ($tableHeaders as $k)
                            @php $nilai = $b[$k['kunci']] ?? ''; @endphp
                            @switch($k['tipe'])
                                @case('utama')
                                    {{-- Teks tebal + keterangan kecil di bawahnya --}}
                                    <td class="pl-5 pr-3">
                                        <p class="truncate font-bold leading-4">{{ $nilai }}</p>
                                        <p class="mt-0.5 text-[11px] leading-4 text-gray-400">{{ $b['keterangan'] ?? 'Data contoh' }}</p>
                                    </td>
                                    @break
                                @case('pil')
                                    {{-- Pil berwarna bila nilainya terdaftar di peta 'pil'; selain itu teks kapital biasa --}}
                                    @php $warna = $k['pil'][$nilai] ?? null; @endphp
                                    <td class="pr-3">
                                        @if ($warna)
                                            <span class="inline-flex h-[22px] items-center justify-center rounded-lg px-4 text-[11px] font-bold {{ ($k['huruf'] ?? true) ? 'uppercase' : '' }} {{ $kelasPil[$warna] ?? $kelasPil['abu'] }}">{{ $nilai }}</span>
                                        @else
                                            <span class="uppercase text-gray-800">{{ $nilai }}</span>
                                        @endif
                                    </td>
                                    @break
                                @default
                                    <td class="truncate pr-3 text-gray-800" title="{{ $nilai }}">{{ $nilai }}</td>
                            @endswitch
                        @endforeach

                        <!-- Kolom Aksi: Dashboard menuju Detail Tracking; halaman lain membuka modal Detail (data baris dibawa lewat data-detail) -->
                        <td>
                            @if ($ruteDetail)
                                <a href="{{ route($ruteDetail, $b['id']) }}"
                                   class="inline-flex h-[28px] w-[49px] items-center justify-center rounded-md bg-tosca-900 text-[11px] font-semibold text-white transition hover:bg-tosca-800">Detail</a>
                            @else
                                <button type="button" data-buka-detail-admin data-detail="{{ json_encode($b, JSON_UNESCAPED_UNICODE) }}"
                                        class="inline-flex h-[28px] w-[49px] items-center justify-center rounded-md bg-tosca-900 text-[11px] font-semibold text-white transition hover:bg-tosca-800">Detail</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                <!-- AKHIR LOOP: Baris data -->

                <!-- Keadaan kosong: tampil jika pencarian/filter tidak menemukan data -->
                @if ($rows->isEmpty())
                    <tr class="h-[57px] border-b border-gray-100">
                        <td colspan="{{ count($tableHeaders) + 1 }}" class="px-5 text-center text-gray-500">Tidak ada data yang sesuai.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Navigasi halaman: komponen paginasi bersama (resources/views/components/pagination.blade.php), dipakai di semua halaman tabel --}}
    <x-pagination :paginasi="$paginasi" />
</section>

</div>

{{-- Modal Detail/Edit (hanya halaman yang tombol Detail-nya membuka modal) --}}
@if ($detail && ! $ruteDetail)
    <x-admin.modal-detail :objek="$detail['objek']" :judul="$detail['judul']" :fields="$detail['fields']" />
@endif

@endsection
