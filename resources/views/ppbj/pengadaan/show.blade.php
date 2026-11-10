@extends('layouts.ppbj')

@section('judul_halaman', 'Metode Pengadaan')
@section('menu_aktif', 'dashboard')
@section('url_kembali', route('ppbj.dashboard'))
@section('breadcrumb_ekstra', 'Metode Pengadaan')

@section('konten')

{{--
    Pencatatan Metode Pengadaan - satu view untuk semua status, ditentukan oleh $pengadaan['status']:
      'diproses' / 'selesai' (default) : form dapat diisi (dropdown metode & jenis, anggaran akhir, vendor, catatan) + zona unggah Dokumen Resmi
      'revisi'                         : seperti default, DITAMBAH kartu "Permintaan Perbaikan" di paling atas
      'tinjauan'                       : hanya-baca; kotak informasi biru di atas, TANPA input unggah dan tanpa tombol Batal/Kirim
    Variabel dari Ppbj\PengadaanController@show:
      $pengadaan : id, status, judul, kode, unit, nilai, ppk, metode, opsi_metode[], jenis, opsi_jenis[], anggaran_akhir, vendor, catatan,
                   dokumen_resmi{judul,keterangan}, revisi{diminta_oleh,peninjau,waktu,penanggung_jawab,catatan}|null
                   - lihat App\Support\Ppbj\DataPpbj::detailPengadaan()

    "Lihat Permohonan" (Detail Permohonan, PPBJ): JANGAN tulis ulang HTML-nya. Route ppbj.permohonan.show merender view yang sudah ada,
    setara dengan: @include('ppk.tracking.tahap.memo', DataTahap::memo() + ['tampilKeputusan' => true, 'layoutRole' => 'ppbj', ...])
    (tampilan sama dengan Detail Permohonan Wadir 2 / PPK: Informasi, Lampiran, Identitas Pengaju, Keputusan Persetujuan, Form Alokasi Anggaran).
--}}

@php
    $hanyaBaca = $pengadaan['status'] === 'tinjauan';
    $revisi = $pengadaan['status'] === 'revisi';

    $kelasIsian = 'h-[44px] w-full rounded-lg border border-gray-200 bg-white px-3 text-[13px] text-gray-900 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20 read-only:cursor-default read-only:bg-[#f6f8fa]';
@endphp

{{-- Kotak informasi biru (hanya status "tinjauan"): proses eksternal tidak dikerjakan di sistem ini --}}
@if ($hanyaBaca)
    <div class="mb-6 flex items-center gap-3 rounded-lg bg-[#eaf2fe] px-4 py-[14px] text-[13px] text-[#2f6fdd]" role="status">
        <svg class="h-[17px] w-[17px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 11v5m0-8h.01"/></svg>
        <p>Pelaksanaan Inaproc / SPSE dilakukan DI LUAR SIMPATIK-PBJ.</p>
    </div>
@endif

{{-- Kartu Permintaan Perbaikan (hanya status "revisi"): siapa yang meminta, kapan, dan apa yang harus diperbaiki --}}
@if ($revisi)
    <section class="mb-6 rounded-2xl border border-gray-100 bg-white px-6 pb-5 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <h2 class="text-[16px] font-bold leading-6">Permintaan Perbaikan</h2>

        <dl class="mt-3 space-y-[11px]">
            <!-- MULAI LOOP: Baris permintaan perbaikan (diminta oleh, peninjau, tanggal & waktu, penanggung jawab, catatan revisi) -->
            @foreach ([
                'Diminta oleh'      => $pengadaan['revisi']['diminta_oleh'],
                'Peninjau'          => $pengadaan['revisi']['peninjau'],
                'Tanggal & Waktu'   => $pengadaan['revisi']['waktu'],
                'Penanggung Jawab'  => $pengadaan['revisi']['penanggung_jawab'],
                'Catatan Revisi'    => $pengadaan['revisi']['catatan'],
            ] as $label => $isi)
                <div class="grid grid-cols-[150px_1fr] gap-x-3 text-[13px] leading-5">
                    <dt class="text-gray-500">{{ $label }}</dt>
                    <dd class="font-medium text-gray-900">{{ $isi }}</dd>
                </div>
            @endforeach
            <!-- AKHIR LOOP: Baris permintaan perbaikan -->
        </dl>

        <!-- Peringatan merah: persetujuan dan tanda tangan tertahan selama revisi belum selesai -->
        <div class="mt-4 flex items-center gap-3 rounded-lg bg-[#fef0f0] px-4 py-[13px] text-[13px] text-[#d92d2d]" role="alert">
            <svg class="h-[17px] w-[17px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
            <p>Persetujuan dan tanda tangan tidak dapat diterapkan selama revisi belum diselesaikan.</p>
        </div>
    </section>
@endif

{{--
    TODO Backend: isi action dengan route penyimpanan metode pengadaan (mis. route('ppbj.pengadaan.update', $pengadaan['id'])).
    Satu form multipart membungkus zona unggah dan form pencatatan. Field: pengadaan_id, metode_pengadaan, jenis_pengadaan,
    anggaran_akhir, vendor, catatan, dokumen_resmi (file pdf/xlsx/jpg maks 10 MB). Validasi ulang di server.
    Pada status "tinjauan" tidak ada form: halaman hanya menampilkan data tersimpan.
--}}
@unless ($hanyaBaca)
<form method="POST" action="" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="pengadaan_id" value="{{ $pengadaan['id'] }}">
@endunless

    <!-- ===================== BARIS ATAS: RINGKASAN PENGADAAN (kiri) + DOKUMEN RESMI (kanan) ===================== -->
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">

        <!-- Tombol "Lihat Permohonan" membuka Detail Permohonan (view yang sudah ada, lihat catatan di atas) -->
        <x-ppbj.kartu-pengadaan :data="$pengadaan" label-tombol="Lihat Permohonan" :url="route('ppbj.permohonan.show', $pengadaan['id'])" />

        <!-- Kartu Dokumen Resmi: zona unggah (status default/revisi) atau dokumen tersimpan hanya-baca (status tinjauan) -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Dokumen Resmi @unless ($hanyaBaca)*@endunless</h2>

            @if ($hanyaBaca)
                <ul class="mt-4">
                    <x-ppbj.berkas :judul="$pengadaan['dokumen_resmi']['judul']" :keterangan="$pengadaan['dokumen_resmi']['keterangan']" />
                </ul>
            @else
                <div class="mt-4">
                    <x-ppbj.zona-unggah name="dokumen_resmi" judul="Unggah Surat Kontrak / SPK / Surat Pesanan" :wajib="! $revisi" />
                </div>
            @endif
        </section>
    </div>

    <!-- ===================== KARTU PENCATATAN METODE PENGADAAN ===================== -->
    <section class="mt-6 rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <h2 class="text-[16px] font-bold leading-6">Pencatatan Metode Pengadaan</h2>

        <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-4 md:grid-cols-2">

            <!-- Metode Pengadaan: dropdown (Pengadaan Langsung, Inaproc, SPSE, Tender, Non-Tender) -->
            <div>
                <label for="metode_pengadaan" class="mb-2 block text-[12px] font-medium leading-4 text-gray-800">Metode Pengadaan *</label>
                @if ($hanyaBaca)
                    <input id="metode_pengadaan" type="text" readonly value="{{ $pengadaan['metode'] }}" class="{{ $kelasIsian }}">
                @else
                    <div class="relative">
                        <select id="metode_pengadaan" name="metode_pengadaan" required class="{{ $kelasIsian }} appearance-none pr-9">
                            <!-- MULAI LOOP: Opsi metode pengadaan dari master data -->
                            @foreach ($pengadaan['opsi_metode'] as $opsi)
                                <option value="{{ $opsi }}" @selected($opsi === $pengadaan['metode'])>{{ $opsi }}</option>
                            @endforeach
                            <!-- AKHIR LOOP: Opsi metode pengadaan -->
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M5.25 7.5h9.5L10 13.25 5.25 7.5z"/></svg>
                    </div>
                @endif
                <p class="mt-2 text-[11px] leading-4 text-gray-500">Pilihan metode: Pengadaan Langsung · Inaproc · SPSE · Tender · Non-Tender</p>
            </div>

            <!-- Jenis Pengadaan: dropdown (Barang / Jasa) -->
            <div>
                <label for="jenis_pengadaan" class="mb-2 block text-[12px] font-medium leading-4 text-gray-800">Jenis Pengadaan *</label>
                @if ($hanyaBaca)
                    <input id="jenis_pengadaan" type="text" readonly value="{{ $pengadaan['jenis'] }}" class="{{ $kelasIsian }}">
                @else
                    <div class="relative">
                        <select id="jenis_pengadaan" name="jenis_pengadaan" required class="{{ $kelasIsian }} appearance-none pr-9">
                            @foreach ($pengadaan['opsi_jenis'] as $opsi)
                                <option value="{{ $opsi }}" @selected($opsi === $pengadaan['jenis'])>{{ $opsi }}</option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M5.25 7.5h9.5L10 13.25 5.25 7.5z"/></svg>
                    </div>
                @endif
            </div>

            <div>
                <label for="anggaran_akhir" class="mb-2 block text-[12px] font-medium leading-4 text-gray-800">Anggaran Akhir</label>
                <input id="anggaran_akhir" name="anggaran_akhir" type="text" inputmode="numeric" value="{{ $pengadaan['anggaran_akhir'] }}" @readonly($hanyaBaca) class="{{ $kelasIsian }}">
            </div>

            <div>
                <label for="vendor" class="mb-2 block text-[12px] font-medium leading-4 text-gray-800">Vendor *</label>
                <input id="vendor" name="vendor" type="text" required value="{{ $pengadaan['vendor'] }}" @readonly($hanyaBaca) class="{{ $kelasIsian }}">
            </div>

            <div class="md:col-span-2">
                <label for="catatan" class="mb-2 block text-[12px] font-medium leading-4 text-gray-800">Catatan</label>
                <textarea id="catatan" name="catatan" rows="3" @readonly($hanyaBaca)
                          class="min-h-[88px] w-full resize-none rounded-lg border border-gray-200 bg-white px-3 py-3 text-[13px] leading-5 text-gray-900 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20 read-only:cursor-default read-only:bg-[#f6f8fa]">{{ $pengadaan['catatan'] }}</textarea>
            </div>
        </div>
    </section>

    <!-- Tombol Batal (kembali ke dashboard) dan Kirim; disembunyikan pada status "tinjauan" -->
    @unless ($hanyaBaca)
        <div class="mt-4 flex justify-end gap-3">
            <a href="{{ route('ppbj.dashboard') }}" class="flex h-10 items-center rounded-lg border border-gray-200 bg-white px-6 text-[13px] font-semibold text-gray-800 transition hover:bg-gray-50">Batal</a>
            <button type="submit" class="h-10 rounded-lg bg-tosca-900 px-6 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Kirim</button>
        </div>
    @endunless

@unless ($hanyaBaca)
</form>
@endunless

@endsection
