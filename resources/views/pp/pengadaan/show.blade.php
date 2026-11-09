@extends('layouts.pp')

@section('judul_halaman', 'Detail Pengadaan')
@section('menu_aktif', 'dashboard')
@section('url_kembali', route('pp.dashboard'))

@section('konten')

{{--
    Detail Pengadaan PP - satu view untuk semua tampilan, ditentukan oleh $pengadaan['status']:
      'diproses' / 'tinjauan' : lencana "Perlu Perbaikan", label "Catatan", tombol "Tambahkan catatan" dan "Konfirmasi Review"
      'ditolak'  : lencana "DITOLAK", kartu "Identitas PPK", label "Catatan Revisi 1.0", tanpa tombol
      'selesai'  : tanpa lencana, label "Catatan Revisi 1.0", tanpa tombol
    Variabel dari Pp\PengadaanController@show:
      $pengadaan : id, status, vendor, nilai_hps, jenis, kode_mak, catatan, versi_catatan, lampiran[], ppk{nama,nip,inisial},
                   tanggal_review, nama_reviewer, judul_review, nomor_surat - lihat App\Support\Pp\DataPengadaan
--}}

@php
    $status = $pengadaan['status'];
    // Status "tinjauan" tampil sama seperti "diproses" (perlu aksi PP)
    $perluAksi = in_array($status, ['diproses', 'tinjauan'], true);
@endphp

<!-- ===================== BARIS UTAMA: PARAMETER & LAMPIRAN (kiri) + IDENTITAS & AKSI (kanan) ===================== -->
<div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_432px]">

    <!-- ---------- KOLOM KIRI ---------- -->
    <div class="space-y-6">

        <!-- Kartu Parameter Pengadaan: judul + lencana status (opsional), lalu pasangan label-isi dua kolom -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-[22px] pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <div class="flex items-center justify-between border-b border-gray-200 pb-[14px]">
                <h2 class="flex items-center gap-2 text-[17px] font-bold leading-6">
                    <svg class="h-5 w-5 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Parameter Pengadaan
                </h2>

                {{-- Lencana status di kanan judul: "Perlu Perbaikan" saat diproses, "DITOLAK" saat ditolak, disembunyikan pada status lain --}}
                @if ($perluAksi)
                    <span class="rounded-full bg-[#fde8ef] px-3 py-[3px] text-[12px] font-medium text-[#d6185a]">Perlu Perbaikan</span>
                @elseif ($status === 'ditolak')
                    <span class="rounded-lg bg-[#fee8e8] px-[26px] py-[4px] text-[12px] font-bold text-[#e5322d]">DITOLAK</span>
                @endif
            </div>

            <dl class="mt-[18px] grid grid-cols-1 gap-x-6 gap-y-[18px] md:grid-cols-2">
                <div>
                    <dt class="text-[11px] font-medium uppercase leading-4 text-gray-500">Nama Penyedia / Vendor</dt>
                    <dd class="mt-[3px] text-[14px] font-bold leading-5">{{ $pengadaan['vendor'] }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-medium uppercase leading-4 text-gray-500">Nilai Pengadaan (HPS)</dt>
                    <!-- TODO Backend: format angka rupiah dari kolom nilai HPS -->
                    <dd class="mt-[3px] text-[17px] font-bold leading-5 text-tosca-400">{{ $pengadaan['nilai_hps'] }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-medium uppercase leading-4 text-gray-500">Jenis Pengadaan</dt>
                    <dd class="mt-[3px] text-[14px] leading-5 text-gray-900">{{ $pengadaan['jenis'] }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-medium uppercase leading-4 text-gray-500">Kode MAK (Mata Anggaran)</dt>
                    <dd class="mt-[3px] text-[14px] font-medium leading-5 text-gray-900">{{ $pengadaan['kode_mak'] }}</dd>
                </div>
            </dl>
        </section>

        <!-- Kartu Lampiran Pendukung PBJ: satu baris per berkas dengan tombol Unduh -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="flex items-center gap-2 border-b border-gray-200 pb-[14px] text-[17px] font-bold leading-6">
                <svg class="h-5 w-5 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                </svg>
                Lampiran Pendukung PBJ
            </h2>

            <ul class="mt-4 space-y-[11px]">
                <!-- MULAI LOOP: Berkas lampiran pengadaan dari database (satu iterasi = satu berkas) -->
                @foreach ($pengadaan['lampiran'] as $berkas)
                    <li class="flex h-[58px] items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-[11px]">
                        <!-- Ikon berkas dalam kotak tosca muda -->
                        <span class="flex h-[32px] w-[32px] shrink-0 items-center justify-center rounded-lg bg-[#e3eef0] text-brand-800">
                            <svg class="h-[16px] w-[16px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5H3.75zM3.75 9.75h16.5M9 9.75v9" />
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[13px] font-bold leading-4">{{ $berkas['nama'] }}</p>
                            <p class="text-[11px] leading-4 text-gray-400">{{ $berkas['ukuran'] }}</p>
                        </div>
                        <!-- TODO Backend: arahkan tautan unduh ke route download berkas (mis. Storage::download) -->
                        <a href="#" download class="flex h-[26px] items-center gap-1.5 rounded-md border border-gray-200 bg-white px-[11px] text-[12px] font-semibold text-gray-900 transition hover:bg-gray-50">
                            <svg class="h-[13px] w-[13px]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Unduh
                        </a>
                    </li>
                @endforeach
                <!-- AKHIR LOOP: Berkas lampiran -->
            </ul>
        </section>
    </div>

    <!-- ---------- KOLOM KANAN ---------- -->
    <div class="space-y-6">

        {{-- Kartu Identitas PPK: hanya tampil pada status "ditolak" --}}
        @if ($status === 'ditolak')
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="border-b border-gray-200 pb-[14px] text-[16px] font-bold leading-6 text-brand-800">Identitas PPK</h2>

                <div class="mt-4 flex items-center gap-[14px]">
                    <!-- TODO Backend: tampilkan foto PPK bila ada; sementara memakai lingkaran berisi inisial -->
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#cce9e4] text-[14px] font-bold text-brand-800">{{ $pengadaan['ppk']['inisial'] }}</span>
                    <div class="min-w-0">
                        <p class="text-[14px] font-bold leading-5">{{ $pengadaan['ppk']['nama'] }}</p>
                        <p class="text-[12px] leading-4 text-gray-500">NIP. {{ $pengadaan['ppk']['nip'] }}</p>
                    </div>
                </div>
            </section>
        @endif

        <!-- Kartu Lembar Rekomendasi & Aksi: catatan (hanya-baca) + tombol aksi bila status "diproses" -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="border-b border-gray-200 pb-[14px] text-[17px] font-bold leading-6">Lembar Rekomendasi &amp; Aksi</h2>

            {{-- Label berbeda menurut status: "Catatan" saat perlu aksi, "Catatan Revisi 1.0" saat hanya dibaca --}}
            <p class="mb-[6px] mt-4 text-[12px] font-semibold text-gray-700">{{ $perluAksi ? 'Catatan' : 'Catatan Revisi ' . $pengadaan['versi_catatan'] }}</p>
            <!-- TODO Backend: isi dari catatan review terakhir pengadaan -->
            <div class="rounded-lg border border-gray-200 bg-[#f3f5f7] px-3 py-2.5 text-[13px] leading-[19px] text-gray-500">{{ $pengadaan['catatan'] }}</div>

            @if ($perluAksi)
                <!-- Tombol "Tambahkan catatan" membuka modal Catatan Review; "Konfirmasi Review" membuka modal Konfirmasi Review -->
                <button type="button" data-buka-modal="modal-catatan-review"
                        class="mt-[22px] h-[42px] w-full rounded-lg border-2 border-tosca-900 bg-white text-[14px] font-semibold text-brand-800 transition hover:bg-[#eef5f6]">Tambahkan catatan</button>
                <button type="button" data-buka-modal="modal-konfirmasi-review"
                        class="mt-3 h-[42px] w-full rounded-lg bg-tosca-900 text-[14px] font-semibold text-white transition hover:bg-tosca-800">Konfirmasi Review</button>
            @endif
        </section>
    </div>
</div>

@if ($perluAksi)
    {{-- Modal hanya dirender saat tombol aksinya tampil --}}
    <!-- Modal Catatan Review (komponen, disembunyikan secara default) -->
    <x-pp.modal-catatan-review :pengadaan-id="$pengadaan['id']" />

    <!-- Modal Konfirmasi Review (komponen, disembunyikan secara default) -->
    <x-pp.modal-konfirmasi-review :pengadaan-id="$pengadaan['id']"
        :teks="$pengadaan['tanggal_review'] . ' · ' . $pengadaan['nama_reviewer'] . '. Telah mereview ' . $pengadaan['judul_review'] . ' dengan no.' . $pengadaan['nomor_surat']" />
@endif

@endsection
