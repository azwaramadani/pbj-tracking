@extends('layouts.'.($layoutRole ?? 'ppk'))

@section('judul_halaman', 'Dokumentasi Penerimaan')
@section('menu_aktif', $menuAktif)
@section('url_kembali', $urlKembali)

@section('konten')

{{--
    Tahap 8 (Pengiriman dan Penerimaan Barang) Alur Perjalanan Pengadaan - "Dokumentasi Penerimaan".
    Variabel dari Ppk\TrackingController@tahap: $penerimaan - lihat App\Support\Ppk\DataTahap::penerimaan()
      banner, nomor, informasi[] (pasangan [label, isi]), status_verifikasi, foto[] (label, url), lampiran[] (judul, berkas)

    TODO Backend: isi action form dengan route penyimpanan penerimaan barang. Satu form multipart membungkus kedua kolom, sehingga
    isian "Diterima oleh" dan berkas lampiran terkirim bersamaan.
    Field: nama_penerima, unit_bagian, tanggal_penerimaan, catatan, dokumen_serah_terima (file), hapus_lampiran (indeks lampiran yang dihapus).
--}}

@php
    // Ikon yang dipakai berulang
    $ikonBerkas = 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 10.5l1.5 1.5 3-3M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z';
    $kelasIsian = 'h-[44px] w-full rounded-lg border border-dashed border-gray-300 bg-[#fafbfc] px-4 text-[12px] text-gray-900 placeholder-gray-500 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20';
@endphp

<!-- Banner sukses hijau di bagian atas -->
<div class="flex items-center gap-3 rounded-lg border border-[#bfe6d0] bg-[#e8f7ef] px-4 py-[13px] text-[13px] text-[#15965b]" role="status">
    <svg class="h-[17px] w-[17px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    <!-- TODO Backend: isi dari nomor dan waktu pencatatan penerimaan -->
    <p>{{ $penerimaan['banner'] }}</p>
</div>

<form id="form-penerimaan" method="POST" action="" enctype="multipart/form-data">
    @csrf

    <div class="mt-6 grid grid-cols-1 items-start gap-6 lg:grid-cols-2">

        <!-- ---------- KOLOM KIRI: Informasi Dokumen Kontrak & SPK + form "Diterima oleh" ---------- -->
        <div class="space-y-6">

            <!-- Kartu Informasi Dokumen Kontrak & SPK: pasangan label (abu-abu) dan isi (tebal) -->
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-5 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="flex items-center gap-2 border-b border-gray-200 pb-[14px] text-[16px] font-bold leading-6">
                    <svg class="h-5 w-5 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikonBerkas }}" /></svg>
                    Informasi Dokumen Kontrak &amp; SPK
                </h2>

                <dl class="mt-[14px] space-y-[9px]">
                    <!-- MULAI LOOP: Baris informasi dokumen kontrak dan SPK -->
                    @foreach ($penerimaan['informasi'] as [$label, $isi])
                        <div class="grid grid-cols-[160px_1fr] gap-x-3 text-[13px] leading-5">
                            <dt class="text-gray-500">{{ $label }}</dt>
                            <dd class="font-bold text-gray-900">{{ $isi }}</dd>
                        </div>
                    @endforeach
                    <!-- AKHIR LOOP: Baris informasi -->
                </dl>
            </section>

            <!-- Kartu "Diterima oleh": isian penerima barang (kosong secara default; teks abu-abu adalah placeholder) -->
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-[16px] font-bold leading-6">Diterima oleh</h2>
                <p class="mt-3 text-[13px] leading-5 text-gray-500">Diisi saat barang diterima. Data belum dikonfirmasi; status saat ini Menunggu Penerimaan.</p>

                <label for="nama_penerima" class="mb-2 mt-[18px] block text-[12px] font-medium leading-4 text-gray-800">Nama *</label>
                <input id="nama_penerima" name="nama_penerima" type="text" required placeholder="Hendra Wijaya (petugas terpilih, belum disimpan)" class="{{ $kelasIsian }}">

                <label for="unit_bagian" class="mb-2 mt-[18px] block text-[12px] font-medium leading-4 text-gray-800">Unit / Bagian *</label>
                <input id="unit_bagian" name="unit_bagian" type="text" required placeholder="Perlengkapan" class="{{ $kelasIsian }}">

                <label for="tanggal_penerimaan" class="mb-2 mt-[18px] block text-[12px] font-medium leading-4 text-gray-800">Tanggal Penerimaan *</label>
                <!-- Berubah menjadi pemilih tanggal saat diklik; placeholder tetap terlihat selama kosong -->
                <input id="tanggal_penerimaan" name="tanggal_penerimaan" type="text" required placeholder="Pilih tanggal penerimaan pada 2026"
                       min="2026-01-01" max="2026-12-31"
                       onfocus="this.type='date'" onblur="if(!this.value){this.type='text'}" class="{{ $kelasIsian }}">

                <label for="catatan" class="mb-2 mt-[18px] block text-[12px] font-medium leading-4 text-gray-800">Catatan</label>
                <input id="catatan" name="catatan" type="text" placeholder="Kondisi, jumlah diterima, dan selisih jika ada..." class="{{ $kelasIsian }}">

                <!-- Tombol kirim: tidak tampak pada potongan mockup; TODO: sesuaikan teksnya dengan desain final -->
                <button type="submit" class="mt-6 h-10 w-full rounded-lg bg-tosca-900 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Konfirmasi Penerimaan</button>
            </section>
        </div>

        <!-- ---------- KOLOM KANAN: Status Verifikasi + Foto Fisik + Lampiran Dokumen Pendukung ---------- -->
        <div class="space-y-6">

            <!-- Kartu Status Verifikasi Barang: label di kiri, lencana kuning di kanan -->
            <section class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white px-6 py-4 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <p class="text-[13px] font-bold">Status Verifikasi Barang:</p>
                <span class="rounded-full bg-[#fef3c7] px-3 py-1 text-[11px] font-semibold text-[#d87705]">{{ $penerimaan['status_verifikasi'] }}</span>
            </section>

            <!-- Kartu Dokumentasi Foto Fisik Barang: tiga foto berdampingan dengan label di pojok kiri bawah -->
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-5 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-[14px] font-bold leading-5">Dokumentasi Foto Fisik Barang (Multi Foto)</h2>

                <div class="mt-4 grid grid-cols-3 gap-3">
                    <!-- MULAI LOOP: Foto fisik barang dari database (satu iterasi = satu foto). TODO Backend: isi "url" dengan alamat foto tersimpan -->
                    @foreach ($penerimaan['foto'] as $foto)
                        <figure class="relative h-[90px] overflow-hidden rounded-lg bg-gradient-to-br from-[#d9c9b0] via-[#b9a58a] to-[#8f7c63]">
                            @if ($foto['url'])
                                <img src="{{ $foto['url'] }}" alt="{{ $foto['label'] }}" class="h-full w-full object-cover">
                            @endif
                            <figcaption class="absolute bottom-1.5 left-1.5 rounded bg-black/55 px-1.5 py-[2px] text-[9px] font-medium text-white">{{ $foto['label'] }}</figcaption>
                        </figure>
                    @endforeach
                    <!-- AKHIR LOOP: Foto fisik barang -->
                </div>

                <!-- Kotak ringkasan: jumlah foto tersimpan dan nomor penerimaan terkait -->
                <div class="mt-4 rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-[10px]">
                    <p class="text-[12px] font-bold leading-4 text-tosca-400">{{ count($penerimaan['foto']) }} foto tersimpan</p>
                    <p class="text-[11px] leading-4 text-gray-500">Dokumentasi terhubung ke {{ $penerimaan['nomor'] }}</p>
                </div>
            </section>

            <!-- Kartu Lampiran Dokumen Pendukung: lampiran tersimpan (dengan tombol hapus) + unggah Dokumen Serah Terima -->
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-[14px] font-bold leading-5">Lampiran Dokumen Pendukung</h2>

                <ul class="mt-4 space-y-3">
                    <!-- MULAI LOOP: Lampiran pendukung dari database (satu iterasi = satu berkas). Tombol sampah mengirim indeks lampiran lewat hapus_lampiran -->
                    @foreach ($penerimaan['lampiran'] as $indeks => $berkas)
                        <li class="flex items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-[10px]">
                            <svg class="h-[18px] w-[18px] shrink-0 text-[#26ae60]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[12px] font-bold leading-4">{{ $berkas['judul'] }}</p>
                                <p class="truncate text-[11px] leading-4 text-gray-500">{{ $berkas['berkas'] }}</p>
                            </div>
                            <button type="submit" name="hapus_lampiran" value="{{ $indeks }}" formnovalidate aria-label="Hapus {{ $berkas['judul'] }}" class="text-gray-500 transition hover:text-[#e5484d]">
                                <svg class="h-[16px] w-[16px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            </button>
                        </li>
                    @endforeach
                    <!-- AKHIR LOOP: Lampiran pendukung -->
                </ul>

                <!-- Unggah Dokumen Serah Terima Penyedia/Vendor (wajib) -->
                <label for="dokumen_serah_terima" class="mb-2 mt-4 block text-[12px] font-medium leading-4 text-gray-700">Dokumen Serah Terima Penyedia/Vendor *</label>
                <input id="dokumen_serah_terima" name="dokumen_serah_terima" type="file" accept=".pdf,.jpg,.jpeg" required
                       class="block w-full text-[12px] text-gray-700 file:mr-3 file:h-8 file:cursor-pointer file:rounded-md file:border file:border-gray-200 file:bg-white file:px-3 file:text-[12px] file:font-semibold file:text-gray-900 hover:file:bg-gray-50">
            </section>
        </div>
    </div>
</form>

@push('skrip')
<script>
    // Selama action form masih kosong (route belum dibuat), pengiriman dicegah agar tidak terjadi galat 405
    document.getElementById('form-penerimaan').addEventListener('submit', (e) => {
        if (e.currentTarget.getAttribute('action') === '') e.preventDefault();
    });
</script>
@endpush

@endsection
