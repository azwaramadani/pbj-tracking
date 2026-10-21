@extends('layouts.unit')

@section('judul_halaman', 'Dashboard')
@section('menu_aktif', 'dashboard')

@section('konten')

@php
    /*
     * Data ringkasan untuk empat kartu statistik di baris atas.
     * 'warna' menentukan warna latar kotak ikon dan warna ikonnya.
     */
    $kartuStatistik = [
        ['label' => 'Total Permohonan', 'jumlah' => 156, 'kotak' => 'bg-[#e6ecee] text-brand-800',
         'ikon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        ['label' => 'Menunggu Approval', 'jumlah' => 23, 'kotak' => 'bg-[#fdf9ec] text-[#f2c84b]',
         'ikon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label' => 'Diproses', 'jumlah' => 18, 'kotak' => 'bg-[#e7f4f3] text-accent',
         'ikon' => 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99'],
        ['label' => 'Selesai', 'jumlah' => 115, 'kotak' => 'bg-[#e8f6ee] text-[#26ae60]',
         'ikon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];

    /* Peta status internal => [label di Dashboard, kelas warna badge]. Data baris dikirim dari route lewat $pengajuanTerbaru */
    $warnaStatus = [
        'diproses' => ['Diproses', 'bg-[#edf4fb] text-[#2f80ed]'],
        'selesai'  => ['Selesai',  'bg-[#e9f6ee] text-[#26ae60]'],
        'menunggu' => ['Pending',  'bg-[#fdf9e6] text-[#f2c84b]'],
        'draft'    => ['Draft',    'bg-gray-100 text-gray-500'],
    ];
@endphp

<!-- ===================== GRID KARTU RINGKASAN (4 kolom) ===================== -->
<section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($kartuStatistik as $kartu)
        <!-- Satu kartu statistik: label + ikon di atas, angka besar + satuan di bawah -->
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <p class="text-[14px] text-gray-500">{{ $kartu['label'] }}</p>
                <!-- Kotak ikon berwarna di kanan atas kartu -->
                <span class="flex h-9 w-9 items-center justify-center rounded-lg {{ $kartu['kotak'] }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $kartu['ikon'] }}" />
                    </svg>
                </span>
            </div>
            <p class="mt-2.5 flex items-baseline gap-2">
                <span class="text-[30px] font-bold leading-9">{{ $kartu['jumlah'] }}</span>
                <span class="text-[13px] text-gray-500">berkas</span>
            </p>
        </div>
    @endforeach
</section>

<!-- ===================== KARTU TABEL "PENGAJUAN TERBARU" ===================== -->
<section class="mt-6 rounded-xl border border-gray-200 bg-white p-6">

    <!-- Baris judul kartu: judul di kiri; pencarian, filter, dan tombol aksi di kanan -->
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-[16px] font-bold">Pengajuan Terbaru</h2>

        <div class="flex items-center gap-3">
            <!-- Kolom pencarian dengan ikon kaca pembesar -->
            <div class="relative">
                <svg class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input type="text" placeholder="Cari nomor surat / judul..."
                       class="h-8 w-[259px] rounded-lg border border-gray-200 bg-white pl-8 pr-3 text-[13px] text-gray-900 placeholder-gray-500 outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/20">
            </div>

            <!-- Tombol Filter -->
            <button type="button" class="flex h-8 items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 text-[13px] font-semibold transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                </svg>
                Filter
            </button>

            <!-- Tombol utama: Buat Pengajuan -->
            <a href="{{ Route::has('unit.permohonan.create') ? route('unit.permohonan.create') : '#' }}"
               class="flex h-8 items-center gap-1.5 rounded-lg bg-brand-800 px-4 text-[13px] font-semibold text-white transition hover:bg-brand-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Buat Pengajuan
            </a>
        </div>
    </div>

    <!-- Tabel daftar pengajuan -->
    <div class="overflow-x-auto">
        <table class="w-full min-w-[820px] table-fixed text-left">
            <!-- Lebar kolom tetap agar sejajar dengan mockup -->
            <colgroup>
                <col class="w-[183px]">
                <col>
                <col class="w-[140px]">
                <col class="w-[160px]">
                <col class="w-[104px]">
            </colgroup>

            <!-- Kepala tabel -->
            <thead class="bg-surface text-[13px] font-medium text-gray-500">
                <tr class="h-10">
                    <th class="px-3 font-medium">No Surat</th>
                    <th class="px-3 font-medium">Judul Pengadaan</th>
                    <th class="px-3 font-medium">Status</th>
                    <th class="px-3 font-medium">Tanggal Pengajuan</th>
                    <th class="px-3 text-center font-medium">Aksi</th>
                </tr>
            </thead>

            <!-- Isi tabel: satu baris per pengajuan -->
            <tbody class="text-[14px]">
                @foreach ($pengajuanTerbaru as $baris)
                    @php [$labelStatus, $kelasBadge] = $warnaStatus[$baris['status']]; @endphp
                    <tr class="h-[47px] border-b border-gray-200">
                        <td class="px-3 font-medium">{{ $baris['no_surat'] }}</td>
                        <td class="truncate px-3">{{ $baris['judul'] }}</td>

                        <!-- Badge status berwarna sesuai peta $warnaStatus -->
                        <td class="px-3">
                            <span class="inline-flex h-[21px] items-center rounded-full px-2.5 text-[12px] font-semibold {{ $kelasBadge }}">
                                {{ $labelStatus }}
                            </span>
                        </td>

                        <td class="px-3 text-gray-500">{{ $baris['tgl_kirim'] }}</td>

                        <!-- Tombol aksi: ikon mata membuka halaman Detail Permohonan -->
                        <td class="px-3 text-center">
                            <a href="{{ route('unit.permohonan.show', $baris['id']) }}" class="inline-flex text-accent transition hover:text-brand-700" aria-label="Lihat detail {{ $baris['no_surat'] }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@endsection
