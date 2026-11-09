@extends('layouts.unit')

@section('judul_halaman', 'Dokumen')
@section('menu_aktif', 'dokumen')

@section('konten')

@php
    /*
     * Variabel dari DokumenController@index:
     *   $dokumen       = paginator berisi kartu dokumen pada halaman aktif
     *   $pilihanJenis, $pilihanBulan, $pilihanStatus = opsi untuk ketiga dropdown filter (kunci => label)
     *   $filter        = nilai filter yang sedang aktif (jenis, tanggal, status; null = semua)
     */

    /* Peta jenis dokumen => kelas warna label di pojok kiri atas kartu */
    $warnaJenis = [
        'kontrak'    => 'bg-[#f2e8ff] text-[#6b21a8]',
        'spk'        => 'bg-[#fde2e2] text-[#991b1b]',
        'pembayaran' => 'bg-[#dff2f0] text-brand-800',
        'permohonan' => 'bg-[#dff2fd] text-[#0369a1]',
    ];

    /* Peta status dokumen => kelas warna lencana di pojok kanan atas kartu */
    $warnaStatus = [
        'aktif'    => 'bg-[#d1f9e4] text-[#055e46]',
        'selesai'  => 'bg-[#d1f9e4] text-[#055e46]',
        'diproses' => 'bg-[#e6f4f8] text-brand-800',
        'menunggu' => 'bg-[#fdf2c6] text-[#d87705]',
    ];

    /* Dropdown filter: [nama query, label judul, teks "semua", opsi, nilai aktif] */
    $dropdown = [
        ['jenis',   'Jenis Dokumen', 'Semua Jenis',   $pilihanJenis,  $filter['jenis']],
        ['tanggal', 'Tanggal',       'Semua Tanggal', $pilihanBulan,  $filter['tanggal']],
        ['status',  'Status',        'Semua Status',  $pilihanStatus, $filter['status']],
    ];
@endphp

<!-- ===================== JUDUL HALAMAN + TOMBOL UNGGAH ===================== -->
<div class="flex flex-wrap items-start justify-between gap-4">
    <div>
        <h2 class="text-[28px] font-bold leading-tight">Dokumen</h2>
        <p class="mt-1 text-[14px] text-gray-500">Akses berkas permohonan, SPK, dan kontrak resmi Anda</p>
    </div>

    <a href="#" class="flex h-10 items-center rounded-lg bg-brand-800 px-5 text-[14px] font-semibold text-white transition hover:bg-brand-700">
        Unggah Dokumen Baru
    </a>
</div>

<!-- ===================== BAR FILTER ===================== -->
<!-- Form GET: setiap dropdown langsung mengirim form saat diubah, sehingga URL (?jenis=&tanggal=&status=) dapat dibagikan -->
<form method="GET" action="{{ route('unit.dokumen.index') }}" class="mt-6 flex flex-wrap items-end justify-between gap-4 rounded-xl border border-gray-200 bg-white p-5">

    <div class="flex flex-wrap items-end gap-4">
        @foreach ($dropdown as [$nama, $judul, $semua, $opsi, $aktif])
            <div>
                <label for="filter-{{ $nama }}" class="mb-1.5 block text-[12px] font-semibold uppercase leading-4 text-gray-500">{{ $judul }}</label>
                <div class="relative">
                    <select id="filter-{{ $nama }}" name="{{ $nama }}" onchange="this.form.submit()"
                            class="h-9 w-[179px] appearance-none rounded-lg border border-gray-200 bg-white pl-3 pr-9 text-[14px] outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/20">
                        <option value="">{{ $semua }}</option>
                        @foreach ($opsi as $kunci => $label)
                            <option value="{{ $kunci }}" @selected($aktif === (string) $kunci)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <!-- Ikon panah bawah dropdown -->
                    <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Tautan untuk menghapus semua filter -->
    <a href="{{ route('unit.dokumen.index') }}" class="flex h-9 items-center text-[14px] font-semibold text-accent transition hover:text-brand-700">Reset Filter</a>
</form>

<!-- ===================== GRID KARTU DOKUMEN (3 kolom, responsif) ===================== -->
<section class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($dokumen as $berkas)
        <!-- Satu kartu dokumen -->
        <article class="flex flex-col rounded-xl border border-gray-200 bg-white p-5">

            <!-- Baris atas: label jenis dokumen (kiri) dan lencana status (kanan) -->
            <div class="flex items-center justify-between">
                <span class="inline-flex h-5 items-center rounded-md px-2.5 text-[12px] font-medium {{ $warnaJenis[$berkas['jenis']] }}">{{ $berkas['label_jenis'] }}</span>
                <span class="inline-flex h-[22px] items-center rounded-full px-2.5 text-[12px] font-semibold {{ $warnaStatus[$berkas['status']] }}">{{ $berkas['label_status'] }}</span>
            </div>

            <!-- Bagian tengah: kotak ikon + judul + ukuran berkas -->
            <div class="mt-5 mb-3 flex gap-3">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-[#f2f4f6] text-brand-800">
                    @if ($berkas['ikon'] === 'gear')
                        <!-- Ikon roda gigi (untuk SPK / kontrak pemeliharaan) -->
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    @else
                        <!-- Ikon dokumen bergaris -->
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    @endif
                </span>

                <div class="min-w-0">
                    <h3 class="text-[15px] font-semibold leading-[18px]">{{ $berkas['judul'] }}</h3>
                    <p class="mt-1 text-[12px] leading-[14px] text-gray-500">PDF &middot; {{ $berkas['ukuran'] }}</p>
                </div>
            </div>

            <!-- Baris bawah: tanggal (kiri) dan tautan unduh (kanan), dipisahkan garis tipis di atasnya -->
            <div class="mt-auto flex items-center justify-between border-t border-gray-100 pt-4">
                <span class="flex items-center gap-1.5 text-[13px] text-gray-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    {{ $berkas['tgl'] }}
                </span>
                <a href="#" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-accent transition hover:text-brand-700">
                    Unduh Berkas
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </article>
    @empty
        <!-- Keadaan kosong: tidak ada dokumen yang cocok dengan filter -->
        <p class="col-span-full rounded-xl border border-gray-200 bg-white py-12 text-center text-[14px] text-gray-500">Tidak ada dokumen yang cocok dengan filter Anda.</p>
    @endforelse
</section>

<!-- ===================== KETERANGAN JUMLAH + PAGINASI ===================== -->
<div class="mt-8 flex items-center justify-between">
    <p class="text-[14px] text-gray-500">Menampilkan {{ $dokumen->count() }} dari {{ $dokumen->total() }} dokumen</p>

    <div class="flex items-center gap-2">
        @if ($dokumen->previousPageUrl())
            <a href="{{ $dokumen->previousPageUrl() }}" class="flex h-[33px] items-center rounded-lg border border-gray-200 bg-white px-4 text-[14px] font-semibold transition hover:bg-gray-50">Sebelumnya</a>
        @else
            <span class="flex h-[33px] cursor-not-allowed items-center rounded-lg border border-gray-200 bg-surface px-4 text-[14px] font-semibold text-gray-500">Sebelumnya</span>
        @endif

        @if ($dokumen->nextPageUrl())
            <a href="{{ $dokumen->nextPageUrl() }}" class="flex h-[33px] items-center rounded-lg border border-gray-200 bg-white px-4 text-[14px] font-semibold transition hover:bg-gray-50">Selanjutnya</a>
        @else
            <span class="flex h-[33px] cursor-not-allowed items-center rounded-lg border border-gray-200 bg-surface px-4 text-[14px] font-semibold text-gray-500">Selanjutnya</span>
        @endif
    </div>
</div>

@endsection
