@extends('layouts.unit')

@section('judul_halaman', 'Tracking Permohonan')
@section('menu_aktif', 'tracking')

@section('konten')

@php
    /*
     * Variabel dari TrackingController@show:
     *   $alur      = 10 tahap pengadaan (nomor, judul, peran, teks, state)
     *              state: 'selesai' = tahap lewat (hijau), 'aktif' = sedang berjalan (biru), 'menunggu' = belum dimulai (abu-abu)
     *   $ringkasan = nilai pagu, pejabat penandatangan, target penyelesaian
     *   $kasus     = contoh status khusus (ditolak, ditangguhkan, perlu revisi, terlambat)
     *   $split     = contoh permohonan yang dipecah menjadi child procurement
     */

    /* Peta jenis kasus => [kelas lencana, kelas titik] */
    $warnaKasus = [
        'tolak'     => ['bg-[#fdf2f2] text-[#dc2626]', 'bg-[#ef4444]'],
        'tangguh'   => ['bg-[#eff4ff] text-[#4f46e5]', 'bg-[#6366f1]'],
        'revisi'    => ['bg-[#fff0f2] text-[#be185d]', 'bg-[#f43f5e]'],
        'terlambat' => ['bg-[#fdf2f2] text-[#b91c1c]', 'bg-[#ef4444]'],
    ];
@endphp

<!-- ===================== BARIS ATAS: ALUR PENGADAAN (kiri) + RINGKASAN BERKAS (kanan) ===================== -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-[3fr_2fr]">

    <!-- Kartu "Alur Perjalanan Pengadaan": timeline vertikal 10 tahap -->
    <section class="rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="mb-6 text-[18px] font-bold leading-6">Alur Perjalanan Pengadaan</h2>

        <ol>
            @foreach ($alur as $tahap)
                @php
                    $selesai = $tahap['state'] === 'selesai';
                    $aktif   = $tahap['state'] === 'aktif';
                @endphp

                <!-- Satu tahap: penanda bulat di kiri, garis penghubung, lalu teks di kanan -->
                <li class="relative flex {{ $loop->last ? '' : 'pb-4' }}">

                    <!-- Garis vertikal penghubung ke tahap berikutnya (tidak ada pada tahap terakhir) -->
                    @unless ($loop->last)
                        <span class="absolute bottom-0 left-[22px] top-0 w-0.5 bg-gray-200" aria-hidden="true"></span>
                    @endunless

                    <!-- Penanda status: centang hijau (selesai), bulat biru berinti putih (aktif), bulat abu-abu (menunggu) -->
                    <div class="w-[37px] shrink-0 pt-0.5">
                        @if ($selesai)
                            <span class="flex h-[18px] w-[18px] items-center justify-center rounded-full bg-[#26ae60] text-white">
                                <svg class="h-[11px] w-[11px]" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                        @elseif ($aktif)
                            <span class="flex h-[18px] w-[18px] items-center justify-center rounded-full bg-[#2f80ed]">
                                <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                            </span>
                        @else
                            <span class="block h-[18px] w-[18px] rounded-full bg-gray-200"></span>
                        @endif
                    </div>

                    <!-- Teks tahap: judul bernomor + penanggung jawab, lalu keterangan status/estimasi -->
                    <div class="min-w-0">
                        <p class="flex flex-wrap items-baseline gap-x-2 leading-5">
                            <span class="text-[14px] font-bold {{ $aktif ? 'text-[#2f80ed]' : '' }}">{{ $tahap['nomor'] }}. {{ $tahap['judul'] }}</span>
                            <span class="text-[11px] font-semibold text-gray-500">&bull; {{ $tahap['peran'] }}</span>
                        </p>
                                                    <p class="text-[12px] leading-5 text-gray-500">{{ $tahap['teks'] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>

    <!-- Kartu "Ringkasan Berkas Pengadaan" (panel samping) -->
    <aside class="rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="text-[16px] font-bold leading-5">Ringkasan Berkas Pengadaan</h2>

        <dl class="mt-4">
            <!-- Perkiraan nilai pagu: angka besar berwarna hijau-teal, diikuti garis pemisah -->
            <div class="border-b border-gray-200 pb-3">
                <dt class="text-[12px] uppercase leading-4 text-gray-500">Perkiraan Nilai Pagu</dt>
                <dd class="mt-0.5 text-[18px] font-bold leading-7 text-accent">{{ $ringkasan['pagu'] }}</dd>
            </div>

            <div class="mt-3.5 space-y-3">
                <div>
                    <dt class="text-[12px] uppercase leading-4 text-gray-500">Pejabat Penandatangan (PPK)</dt>
                    <dd class="mt-1 text-[14px] leading-5">{{ $ringkasan['pejabat'] }}</dd>
                </div>
                <div>
                    <dt class="text-[12px] uppercase leading-4 text-gray-500">Target Penyelesaian</dt>
                    <dd class="mt-1 text-[14px] leading-5">{{ $ringkasan['target'] }}</dd>
                </div>
            </div>
        </dl>
    </aside>
</div>

<!-- ===================== KARTU "STATUS & STATE PERMOHONAN" ===================== -->
<section class="mt-6 rounded-xl border border-gray-200 bg-white p-6">
    <h2 class="text-[18px] font-bold leading-6">Status &amp; State Permohonan</h2>
    <p class="mt-0.5 text-[13px] leading-5 text-gray-500">Ringkasan status, alasan, catatan reviewer, dan contoh split permohonan yang tetap terlacak ke induknya.</p>

    <!-- Grid 2 kolom berisi kartu status khusus -->
    <div class="mt-[18px] grid grid-cols-1 gap-3 md:grid-cols-2">
        @foreach ($kasus as $item)
            @php [$kelasLencana, $kelasTitik] = $warnaKasus[$item['warna']]; @endphp
            <div class="rounded-xl border border-gray-200 bg-surface p-4">
                <!-- Baris atas: lencana jenis status di kiri, jumlah kasus di kanan -->
                <div class="flex items-center justify-between">
                    <span class="inline-flex h-[21px] items-center gap-1.5 rounded-md px-2 text-[12px] font-medium {{ $kelasLencana }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $kelasTitik }}"></span>
                        {{ $item['label'] }}
                    </span>
                    <span class="text-[12px] font-semibold">{{ $item['jumlah'] }} kasus</span>
                </div>
                <p class="mt-2 text-[13px] font-semibold leading-5">{{ $item['judul'] }}</p>
                <p class="mt-1.5 text-[12px] leading-[18px] text-gray-500">{{ $item['keterangan'] }}</p>
            </div>
        @endforeach
    </div>

    <!-- Kartu lebar penuh: contoh permohonan yang dipecah (split) -->
    <div class="mt-3 rounded-xl border border-gray-200 bg-surface p-4">
        <div class="flex items-center justify-between">
            <span class="inline-flex h-[21px] items-center gap-1.5 rounded-md bg-[#f2f4f6] px-2 text-[12px] font-medium text-gray-500">
                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                Permohonan Dipecah
            </span>
            <span class="text-[12px] font-semibold">{{ $split['jumlah'] }} child procurement</span>
        </div>
        <p class="mt-2 text-[13px] font-semibold leading-5">{{ $split['judul'] }}</p>

        <!-- Daftar permohonan induk dan child; kode dicetak tebal -->
        <ul class="mt-2 space-y-[3px]">
            @foreach ($split['daftar'] as $anak)
                <li class="flex items-center gap-2 text-[12px] leading-5">
                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-accent"></span>
                    <span><span class="font-bold">{{ $anak['kode'] }}</span> &mdash; {{ $anak['keterangan'] }}</span>
                </li>
            @endforeach
        </ul>

        <p class="mt-3 text-[12px] leading-[18px] text-gray-500">{{ $split['catatan'] }}</p>
    </div>
</section>

@endsection
