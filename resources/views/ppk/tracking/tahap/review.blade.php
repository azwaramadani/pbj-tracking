@extends('layouts.'.($layoutRole ?? 'ppk'))

@section('judul_halaman', 'Detail Pengadaan')
@section('menu_aktif', $menuAktif)
@section('url_kembali', $urlKembali)

@section('konten')

{{--
    Tahap 6 (Review PP/PPK) Alur Perjalanan Pengadaan - hanya-baca, memakai komponen Detail Pengadaan yang sama dengan Modul PP/PPK.
      $state = 'aktif'   : review belum selesai -> kartu Parameter (kiri) dan Lampiran Pendukung PBJ (kanan) saja
      $state = 'selesai' : review sudah selesai -> Parameter + Lampiran (kiri), Lembar Rekomendasi & Aksi + Tanda Tangan Pengesahan PP (kanan)
    Variabel dari Ppk\TrackingController@tahap: $pengadaan (lihat App\Support\Pp\DataPengadaan), $tanda_tangan, $state
--}}

@if ($state === 'aktif')
    <!-- ===================== TAHAP SEDANG BERJALAN: PARAMETER (kiri) + LAMPIRAN (kanan) ===================== -->
    <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-2">
        <x-pp.parameter-pengadaan :pengadaan="$pengadaan" />
        <x-pp.lampiran-pbj :lampiran="$pengadaan['lampiran']" />
    </div>
@else
    <!-- ===================== TAHAP SELESAI: PARAMETER + LAMPIRAN (kiri) + REKOMENDASI & TANDA TANGAN (kanan) ===================== -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_432px]">

        <div class="space-y-6">
            <x-pp.parameter-pengadaan :pengadaan="$pengadaan" />
            <x-pp.lampiran-pbj :lampiran="$pengadaan['lampiran']" />
        </div>

        <div class="space-y-6">

            <!-- Kartu Lembar Rekomendasi & Aksi: catatan revisi (hanya-baca, tanpa tombol aksi) -->
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="border-b border-gray-200 pb-[14px] text-[17px] font-bold leading-6">Lembar Rekomendasi &amp; Aksi</h2>

                <p class="mb-[6px] mt-4 text-[12px] font-semibold text-gray-700">Catatan Revisi {{ $pengadaan['versi_catatan'] }}</p>
                <!-- TODO Backend: isi dari catatan revisi terakhir pengadaan -->
                <div class="rounded-lg border border-gray-200 bg-[#f3f5f7] px-3 py-2.5 text-[13px] leading-[19px] text-gray-500">{{ $pengadaan['catatan'] }}</div>
            </section>

            <!-- Kartu Tanda Tangan Pengesahan PP: berkas pengesahan yang diunggah PP + keterangan -->
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <div class="flex items-start gap-2.5 border-b border-gray-200 pb-[12px]">
                    <svg class="mt-1 h-[18px] w-[18px] shrink-0 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 14v4.75A2.25 2.25 0 0117.25 21h-11A2.25 2.25 0 014 18.75v-11A2.25 2.25 0 016.25 5.5H11" />
                    </svg>
                    <div>
                        <h2 class="text-[16px] font-bold leading-5">Tanda Tangan Pengesahan PP</h2>
                        <p class="text-[11px] leading-4 text-gray-500">Lakukan pengisian untuk pengesahan keputusan akhir PP</p>
                    </div>
                </div>

                <!-- Berkas pengesahan: ikon, nama, ukuran, dan tombol Unduh. TODO Backend: arahkan ke route unduh berkas pengesahan -->
                <div class="mt-4 flex h-[54px] items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-[11px]">
                    <span class="flex h-[32px] w-[32px] shrink-0 items-center justify-center rounded-lg bg-[#e3eef0] text-brand-800">
                        <svg class="h-[16px] w-[16px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5H3.75zM3.75 9.75h16.5M9 9.75v9" /></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-bold leading-4">{{ $tanda_tangan['berkas']['nama'] }}</p>
                        <p class="text-[11px] leading-4 text-gray-400">{{ $tanda_tangan['berkas']['ukuran'] }}</p>
                    </div>
                    <a href="#" download class="flex h-[26px] items-center gap-1.5 rounded-md border border-gray-200 bg-white px-[11px] text-[12px] font-semibold text-gray-900 transition hover:bg-gray-50">
                        <svg class="h-[13px] w-[13px]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                        Unduh
                    </a>
                </div>

                <p class="mt-3 text-[12px] leading-[18px] text-gray-500">{{ $tanda_tangan['keterangan'] }}</p>
            </section>
        </div>
    </div>
@endif

@endsection
