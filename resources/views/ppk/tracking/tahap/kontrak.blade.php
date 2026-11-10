@extends('layouts.'.($layoutRole ?? 'ppk'))

@section('judul_halaman', 'Detail Kontrak')
@section('menu_aktif', $menuAktif)
@section('url_kembali', $urlKembali)
@section('breadcrumb_ekstra', 'Detail Kontrak / SPK')

@section('konten')

{{--
    Tahap 7 (Proses Pengadaan dan Kontrak/SPK) Alur Perjalanan Pengadaan - "Detail Kontrak / SPK", hanya-baca.
    Variabel dari Ppk\TrackingController@tahap: $kontrak - lihat App\Support\Ppk\DataTahap::kontrak()
      judul, kode, unit, nilai, ppk, pengadaan_id, informasi[] (pasangan [label, isi]), pengingat[] (pasangan [label, isi]),
      status_barang, dokumen[] (judul, keterangan)
    Dipakai juga oleh role PPBJ (Detail Kontrak): kirim $layoutRole = 'ppbj' dan $kontrak['id'] + $kontrak['status'] (tinjauan | diproses | selesai);
    tombol utama di bawah ringkasan berubah menurut status.
--}}

@php
    // Ikon berkas bercentang yang dipakai berulang pada daftar dokumen
    $ikonBerkas = 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 10.5l1.5 1.5 3-3M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z';
@endphp

<!-- ===================== BARIS UTAMA: KONTRAK (kiri) + PENGINGAT & DOKUMEN (kanan) ===================== -->
<div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[minmax(0,1fr)_382px]">

    <!-- ---------- KOLOM KIRI: ringkasan pengadaan + tombol Lihat Pengadaan + Informasi Kontrak ---------- -->
    <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <h2 class="text-[18px] font-bold leading-6">{{ $kontrak['judul'] }}</h2>
        <p class="mt-[3px] text-[11px] leading-4 text-gray-500">{{ $kontrak['kode'] }}</p>

        <!-- Ringkasan: Unit, Nilai Pengadaan, dan PPK -->
        <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-3">
            <div>
                <dt class="text-[12px] leading-4 text-gray-500">Unit</dt>
                <dd class="mt-[5px] text-[14px] leading-5">{{ $kontrak['unit'] }}</dd>
            </div>
            <div>
                <dt class="text-[12px] leading-4 text-gray-500">Nilai Pengadaan</dt>
                <dd class="mt-[5px] text-[14px] leading-5">{{ $kontrak['nilai'] }}</dd>
            </div>
            <div>
                <dt class="text-[12px] leading-4 text-gray-500">PPK</dt>
                <dd class="mt-[5px] text-[14px] leading-5">{{ $kontrak['ppk'] }}</dd>
            </div>
        </dl>

        @if (($layoutRole ?? 'ppk') === 'ppbj')
            {{--
                Tombol utama PPBJ menurut status kontrak:
                  'tinjauan'            : "Unggah Dokumen" -> form Unggah / Edit Kontrak & SPK (ppbj.kontrak.edit)
                  'diproses' / 'selesai': "Lihat Penerimaan" -> Dokumentasi Penerimaan versi hanya-baca (ppbj.kontrak.penerimaan)
            --}}
            @if ($kontrak['status'] === 'tinjauan')
                <a href="{{ route('ppbj.kontrak.edit', $kontrak['id']) }}"
                   class="mt-4 flex h-[38px] w-full items-center justify-center rounded-lg bg-tosca-900 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Unggah Dokumen</a>
            @else
                <a href="{{ route('ppbj.kontrak.penerimaan', $kontrak['id']) }}"
                   class="mt-4 flex h-[38px] w-full items-center justify-center rounded-lg bg-tosca-900 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Lihat Penerimaan</a>
            @endif
        @elseif (($layoutRole ?? 'ppk') === 'ppk')
            <!-- Tombol "Lihat Pengadaan" (hanya PPK; Detail Pengadaan milik modul PPK): membuka Detail Pengadaan terkait. TODO Backend: ganti id contoh dengan id pengadaan milik kontrak -->
            <a href="{{ route('ppk.pengadaan.show', $kontrak['pengadaan_id']) }}"
               class="mt-4 flex h-[38px] w-full items-center justify-center rounded-lg border border-gray-200 bg-white text-[13px] font-semibold text-brand-800 transition hover:bg-gray-50">Lihat Pengadaan</a>
        @endif

        <!-- Kartu Informasi Kontrak: pasangan label (abu-abu) dan isi -->
        <div class="mt-4 rounded-xl border border-gray-200 px-6 pb-5 pt-5">
            <h3 class="text-[16px] font-bold leading-6">Informasi Kontrak</h3>

            <dl class="mt-[10px] space-y-[11px]">
                <!-- MULAI LOOP: Baris informasi kontrak (nomor dokumen, jenis, vendor, nilai, tanggal, termin, status) -->
                @foreach ($kontrak['informasi'] as [$label, $isi])
                    <div class="grid grid-cols-[160px_1fr] gap-x-3 text-[13px] leading-5">
                        <dt class="text-gray-500">{{ $label }}</dt>
                        <dd class="font-medium text-gray-900">{{ $isi }}</dd>
                    </div>
                @endforeach
                <!-- AKHIR LOOP: Baris informasi kontrak -->
            </dl>
        </div>
    </section>

    <!-- ---------- KOLOM KANAN ---------- -->
    <div class="space-y-6">

        <!-- Kartu Pengingat Pengiriman: target, jadwal notifikasi, penerima, dan kotak status barang -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-5 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Pengingat Pengiriman</h2>

            <dl class="mt-[10px] space-y-[11px]">
                <!-- MULAI LOOP: Baris pengingat (target, jadwal notifikasi, penerima) -->
                @foreach ($kontrak['pengingat'] as [$label, $isi])
                    <div class="grid grid-cols-[110px_1fr] gap-x-3 text-[13px] leading-5">
                        <dt class="text-gray-500">{{ $label }}</dt>
                        <dd class="font-medium text-gray-900">{{ $isi }}</dd>
                    </div>
                @endforeach
                <!-- AKHIR LOOP: Baris pengingat -->
            </dl>

            <!-- Kotak informasi biru: status penerimaan barang -->
            <div class="mt-4 flex items-center gap-3 rounded-lg bg-[#eaf2fe] px-4 py-[14px] text-[13px] text-[#2f6fdd]" role="status">
                <svg class="h-[17px] w-[17px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 11v5m0-8h.01"/></svg>
                <p>{{ $kontrak['status_barang'] }}</p>
            </div>
        </section>

        <!-- Kartu Dokumen Kontrak & Pendukung: satu baris per dokumen dengan tautan Lihat / Unduh -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Dokumen Kontrak &amp; Pendukung</h2>

            <ul class="mt-4 space-y-3">
                <!-- MULAI LOOP: Dokumen kontrak dan pendukung dari database (satu iterasi = satu dokumen) -->
                @foreach ($kontrak['dokumen'] as $dokumen)
                    <li class="flex items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-[10px]">
                        <svg class="h-[17px] w-[17px] shrink-0 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikonBerkas }}" /></svg>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-bold leading-4">{{ $dokumen['judul'] }}</p>
                            <p class="mt-[3px] text-[11px] leading-[14px] text-gray-500">{{ $dokumen['keterangan'] }}</p>
                        </div>
                        <!-- TODO Backend: arahkan tautan ke route lihat/unduh dokumen -->
                        <a href="#" download class="shrink-0 text-[11px] font-bold text-tosca-400 transition hover:text-tosca-600">Lihat / Unduh</a>
                    </li>
                @endforeach
                <!-- AKHIR LOOP: Dokumen kontrak dan pendukung -->
            </ul>
        </section>
    </div>
</div>

@endsection
