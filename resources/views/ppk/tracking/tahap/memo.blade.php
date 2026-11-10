@extends('layouts.'.($layoutRole ?? 'ppk'))

@section('judul_halaman', 'Detail Permohonan')
@section('menu_aktif', $menuAktif)
@section('url_kembali', $urlKembali)

@section('konten')

{{--
    Tahap 4 (Memo Wadir 2 dan PPK) dan tahap 5 (PPBJ) Alur Perjalanan Pengadaan - hanya-baca, tanpa tombol aksi.
      $tampilKeputusan = false (tahap 4): kolom kanan = Identitas Pengaju + Form Alokasi Anggaran (MAK)
      $tampilKeputusan = true  (tahap 5): ditambah kartu "Keputusan Persetujuan" berisi memo persetujuan Wadir 2 dan PPK
    Variabel dari Ppk\TrackingController@tahap: $permohonan (lihat App\Support\Wadir2\DataMak), $memo[], $tampilKeputusan
    Dipakai juga oleh role PPBJ (Detail Permohonan, tombol "Lihat Permohonan"): kirim $layoutRole = 'ppbj' agar layout/sidebar PPBJ yang dipakai.
--}}

<!-- ===================== BARIS UTAMA: INFORMASI & LAMPIRAN (kiri) + IDENTITAS, KEPUTUSAN & ANGGARAN (kanan) ===================== -->
<div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_386px]">

    <!-- ---------- KOLOM KIRI: komponen bersama Informasi Pengadaan + Lampiran Dokumen Pendukung ---------- -->
    <x-wadir2.informasi-pengadaan :permohonan="$permohonan" />

    <!-- ---------- KOLOM KANAN ---------- -->
    <div class="space-y-6">

        <!-- Kartu Identitas Pengaju: foto/inisial, nama, dan NIP -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="border-b border-gray-200 pb-[14px] text-[16px] font-bold leading-6 text-brand-800">Identitas Pengaju</h2>

            <div class="mt-4 flex items-center gap-[14px]">
                <!-- TODO Backend: tampilkan foto pengaju bila ada; sementara memakai lingkaran berisi inisial -->
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#cce9e4] text-[14px] font-bold text-brand-800">{{ $permohonan['pengaju']['inisial'] }}</span>
                <div class="min-w-0">
                    <p class="text-[14px] font-bold leading-5">{{ $permohonan['pengaju']['nama'] }}</p>
                    <p class="text-[12px] leading-4 text-gray-500">NIP. {{ $permohonan['pengaju']['nip'] }}</p>
                </div>
            </div>
        </section>

        {{-- Kartu Keputusan Persetujuan: hanya pada tahap 5 (PPBJ); memo Wadir 2 dan PPK ditampilkan hanya-baca --}}
        @if ($tampilKeputusan)
            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-[16px] font-bold leading-6 text-brand-800">Keputusan Persetujuan</h2>
                <p class="mt-[10px] text-[13px] leading-[18px] text-gray-500">Lakukan peninjauan secara teliti pada lampiran rincian anggaran sebelum mengambil keputusan.</p>

                <!-- MULAI LOOP: Memo persetujuan (Wadir 2, PPK) dari database. TODO Backend: isi dari catatan memo yang sudah diterbitkan -->
                @foreach ($memo as $m)
                    <label for="{{ $m['nama'] }}" class="mb-[6px] mt-[14px] block text-[12px] font-semibold text-gray-800">{{ $m['label'] }}</label>
                    <input id="{{ $m['nama'] }}" name="{{ $m['nama'] }}" type="text" readonly value="{{ $m['isi'] }}"
                           class="h-10 w-full cursor-default rounded-lg border border-gray-200 bg-[#f3f5f7] px-3 text-[13px] text-gray-700 outline-none">
                @endforeach
                <!-- AKHIR LOOP: Memo persetujuan -->
            </section>
        @endif

        <!-- Kartu Form Alokasi Anggaran (MAK): terisi dan hanya-baca -->
        <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6 {{ $tampilKeputusan ? '' : 'text-brand-800' }}">Form Alokasi Anggaran (MAK)</h2>

            <label for="mata_anggaran_kegiatan" class="mb-[6px] mt-[14px] block text-[12px] font-semibold text-gray-800">Mata Anggaran Kegiatan (MAK)</label>
            <div class="relative">
                <input id="mata_anggaran_kegiatan" name="mata_anggaran_kegiatan" type="text" readonly value="{{ $permohonan['anggaran']['mata_anggaran'] }}"
                       class="h-10 w-full cursor-default rounded-lg border border-gray-200 bg-white pl-3 pr-9 text-[13px] text-gray-900 outline-none">
                <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>

            <label for="jumlah_alokasi" class="mb-[6px] mt-[14px] block text-[12px] font-semibold text-gray-800">Jumlah Alokasi</label>
            <div class="flex h-10 overflow-hidden rounded-lg border border-gray-200">
                <span class="flex w-[42px] shrink-0 items-center justify-center bg-[#e9ecef] text-[13px] font-semibold text-gray-700">Rp</span>
                <input id="jumlah_alokasi" name="jumlah_alokasi" type="text" readonly value="{{ $permohonan['anggaran']['jumlah_alokasi'] }}"
                       class="min-w-0 flex-1 cursor-default bg-white px-3 text-[13px] text-gray-900 outline-none">
            </div>
        </section>
    </div>
</div>

@endsection
