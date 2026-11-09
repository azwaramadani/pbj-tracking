@extends('layouts.ppk')

@section('judul_halaman', 'Detail Permohonan')
@section('menu_aktif', 'tracking')
@section('url_kembali', route('ppk.tracking.index'))

@section('konten')

{{--
    Detail Tracking PPK - informasi pengadaan (kiri) dan "Alur Perjalanan Pengadaan" 10 tahap (kanan).
    Variabel dari Ppk\TrackingController@show:
      $pengajuan : id, status, nomor_surat, unit_pengaju, judul, tujuan, total_anggaran,
                   alur[] (tiap item: nomor, judul, pelaku, state, teks) - lihat App\Support\Ppk\DataTracking
      state tahap: 'selesai' = centang hijau, 'aktif' = titik biru (sedang berjalan), 'menunggu' = titik abu-abu (belum dimulai)
    TODO: detail tahap yang dapat diklik akan ditambahkan setelah desainnya tersedia.
--}}

<!-- ===================== BARIS UTAMA: INFORMASI PENGADAAN (kiri) + ALUR PERJALANAN (kanan) ===================== -->
<div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[6fr_7fr]">

    <!-- ---------- KOLOM KIRI: Informasi Pengadaan (label dan isi bertumpuk satu kolom) ---------- -->
    <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <h2 class="border-b border-gray-200 pb-[18px] text-[16px] font-bold leading-6 text-brand-800">Informasi Pengadaan</h2>

        <dl class="mt-[18px] space-y-[18px]">
            <div>
                <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">Nomor Surat Pernyataan</dt>
                <dd class="mt-[3px] text-[14px] leading-5 text-gray-900">{{ $pengajuan['nomor_surat'] }}</dd>
            </div>
            <div>
                <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">Unit Pengaju</dt>
                <dd class="mt-[3px] text-[14px] font-medium leading-5 text-gray-900">{{ $pengajuan['unit_pengaju'] }}</dd>
            </div>
            <div>
                <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">Judul Pengajuan</dt>
                <dd class="mt-[3px] text-[14px] leading-5 text-gray-900">{{ $pengajuan['judul'] }}</dd>
            </div>
            <div>
                <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">Tujuan Pengadaan</dt>
                <dd class="mt-[3px] text-[14px] leading-[22px] text-gray-900">{{ $pengajuan['tujuan'] }}</dd>
            </div>
            <div>
                <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">Total Estimasi Anggaran</dt>
                <!-- TODO Backend: format angka rupiah dari kolom nilai anggaran -->
                <dd class="mt-[3px] text-[18px] font-bold leading-7 text-tosca-400">{{ $pengajuan['total_anggaran'] }}</dd>
            </div>
        </dl>
    </section>

    <!-- ---------- KOLOM KANAN: Alur Perjalanan Pengadaan (timeline vertikal 10 tahap) ---------- -->
    <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <h2 class="text-center text-[17px] font-bold leading-6">Alur Perjalanan Pengadaan</h2>

        <ol class="mt-6">
            <!-- MULAI LOOP: Tahap alur pengadaan dari database (satu iterasi = satu tahap; urutan tetap 1 sampai 10) -->
            @foreach ($pengajuan['alur'] as $tahap)
                @php
                    $selesai = $tahap['state'] === 'selesai';
                    $aktif   = $tahap['state'] === 'aktif';
                @endphp

                <!-- Satu tahap: penanda bulat + garis penghubung di kiri, teks di kanan (baris genap diberi latar abu-abu muda) -->
                <li class="relative flex">

                    <!-- Garis vertikal penghubung ke tahap berikutnya (tidak ada pada tahap terakhir) -->
                    @unless ($loop->last)
                        <span class="absolute bottom-0 left-[8px] top-[12px] w-px bg-gray-300" aria-hidden="true"></span>
                    @endunless

                    <!-- Penanda status: centang hijau (selesai), titik biru berinti putih (sedang berjalan), bulat abu-abu (belum dimulai) -->
                    <div class="relative z-10 w-[34px] shrink-0 pt-[2px]">
                        @if ($selesai)
                            <span class="flex h-[17px] w-[17px] items-center justify-center rounded-full bg-[#26ae60] text-white">
                                <svg class="h-[10px] w-[10px]" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                        @elseif ($aktif)
                            <span class="flex h-[17px] w-[17px] items-center justify-center rounded-full bg-[#2f80ed]">
                                <span class="h-[7px] w-[7px] rounded-full bg-white"></span>
                            </span>
                        @else
                            <span class="block h-[17px] w-[17px] rounded-full bg-[#e5e7eb]"></span>
                        @endif
                    </div>

                    <!-- Teks tahap: judul bernomor (biru bila aktif) + pelaku, lalu keterangan. Tahap selesai/berjalan berupa tautan ke detail tahap; tahap "Yang akan datang" bukan tautan -->
                    @php $bisaDibuka = $tahap['state'] !== 'menunggu'; @endphp
                    <{{ $bisaDibuka ? 'a' : 'div' }}
                        @if ($bisaDibuka) href="{{ route('ppk.tracking.tahap', [$pengajuan['id'], $tahap['nomor']]) }}" @endif
                        class="block min-w-0 flex-1 px-2 py-[6px] {{ $loop->even ? 'bg-[#f5f7f8]' : '' }} {{ $bisaDibuka ? 'cursor-pointer transition hover:bg-[#eef5f6]' : '' }}">
                        <p class="flex flex-wrap items-baseline gap-x-2 leading-5">
                            <span class="text-[13.5px] font-bold {{ $aktif ? 'text-[#2f80ed]' : 'text-gray-900' }}">{{ $tahap['nomor'] }}. {{ $tahap['judul'] }}</span>
                            @if ($tahap['pelaku'] !== '')
                                <span class="text-[10px] font-semibold text-gray-500">{{ $tahap['state'] === 'menunggu' ? '• ' : '' }}{{ $tahap['pelaku'] }}</span>
                            @endif
                        </p>
                        <p class="text-[11.5px] leading-[18px] text-gray-500">{{ $tahap['teks'] }}</p>
                    </{{ $bisaDibuka ? 'a' : 'div' }}>
                </li>
            @endforeach
            <!-- AKHIR LOOP: Tahap alur pengadaan -->
        </ol>
    </section>
</div>

@endsection
