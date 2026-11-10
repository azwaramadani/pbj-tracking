@extends('layouts.ppbj')

@section('judul_halaman', 'Unggah Kontrak & SPK')
@section('menu_aktif', 'kontrak')
@section('url_kembali', route('ppbj.kontrak.show', $kontrak['id']))
@section('breadcrumb_ekstra', 'Unggah / Edit Kontrak & SPK')

@section('konten')

{{--
    Unggah / Edit Kontrak & SPK (dibuka dari tombol "Unggah Dokumen" pada Detail Kontrak berstatus tinjauan).
    Variabel dari Ppbj\KontrakController@edit:
      $kontrak : ringkasan pengadaan (judul, kode, unit, nilai, ppk, pengadaan_id) - lihat App\Support\Ppbj\DataPpbj::detailKontrak()
      $form    : nilai awal field Data Dokumen dan opsi dropdown - lihat DataPpbj::formKontrak()

    TODO Backend: isi action dengan route penyimpanan kontrak (mis. route('ppbj.kontrak.update', $kontrak['id'])).
    Satu form multipart membungkus zona unggah dan Data Dokumen. Field: dokumen_resmi (file pdf/xlsx/jpg maks 10 MB), nomor_dokumen,
    nomor_pengadaan, nilai, status, tanggal_terbit, tanggal_mulai, target_pengiriman, tanggal_pengingat. Validasi ulang di server.
--}}

@php
    $kelasIsian = 'h-[44px] w-full rounded-lg border border-gray-200 bg-white px-3 text-[13px] text-gray-900 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20';
    $ikonChevron = 'M5.25 7.5h9.5L10 13.25 5.25 7.5z';
@endphp

<form method="POST" action="" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="kontrak_id" value="{{ $kontrak['id'] }}">

    <!-- ===================== BARIS ATAS: RINGKASAN PENGADAAN (kiri) + DOKUMEN RESMI (kanan) ===================== -->
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
        <!-- Tombol "Lihat Pengadaan" membuka Metode Pengadaan terkait -->
        <x-ppbj.kartu-pengadaan :data="$kontrak" label-tombol="Lihat Pengadaan" :url="route('ppbj.pengadaan.show', $kontrak['pengadaan_id'])" />

        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Dokumen Resmi *</h2>
            <div class="mt-4">
                <x-ppbj.zona-unggah name="dokumen_resmi" judul="Unggah Surat Kontrak / SPK / Surat Pesanan" wajib />
            </div>
        </section>
    </div>

    <!-- ===================== KARTU DATA DOKUMEN ===================== -->
    <section class="mt-6 rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <h2 class="text-[16px] font-bold leading-6">Data Dokumen</h2>

        <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-4 md:grid-cols-2">

            <div>
                <label for="nomor_dokumen" class="mb-2 block text-[12px] font-medium leading-4 text-gray-800">Nomor Dokumen *</label>
                <input id="nomor_dokumen" name="nomor_dokumen" type="text" required value="{{ $form['nomor_dokumen'] }}" class="{{ $kelasIsian }}">
            </div>

            <!-- Nomor Pengadaan: dropdown. TODO Backend: isi opsi dengan pengadaan milik PPBJ -->
            <div>
                <label for="nomor_pengadaan" class="mb-2 block text-[12px] font-medium leading-4 text-gray-800">Nomor Pengadaan *</label>
                <div class="relative">
                    <select id="nomor_pengadaan" name="nomor_pengadaan" required class="{{ $kelasIsian }} appearance-none pr-9">
                        <!-- MULAI LOOP: Opsi nomor pengadaan -->
                        @foreach ($form['opsi_pengadaan'] as $opsi)
                            <option value="{{ $opsi }}" @selected($opsi === $form['nomor_pengadaan'])>{{ $opsi }}</option>
                        @endforeach
                        <!-- AKHIR LOOP: Opsi nomor pengadaan -->
                    </select>
                    <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="{{ $ikonChevron }}"/></svg>
                </div>
            </div>

            <p class="-mt-2 text-[11px] leading-4 text-gray-500 md:col-span-2">Pilihan jenis: Surat Kontrak · SPK · Surat Pesanan</p>

            <div>
                <label for="nilai" class="mb-2 block text-[12px] font-medium leading-4 text-gray-800">Nilai *</label>
                <input id="nilai" name="nilai" type="text" inputmode="numeric" required value="{{ $form['nilai'] }}" class="{{ $kelasIsian }}">
            </div>

            <div>
                <label for="status" class="mb-2 block text-[12px] font-medium leading-4 text-gray-800">Status *</label>
                <div class="relative">
                    <select id="status" name="status" required class="{{ $kelasIsian }} appearance-none pr-9">
                        @foreach ($form['opsi_status'] as $opsi)
                            <option value="{{ $opsi }}" @selected($opsi === $form['status'])>{{ $opsi }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="{{ $ikonChevron }}"/></svg>
                </div>
            </div>

            <!-- Isian tanggal memakai pemilih tanggal bawaan peramban -->
            @foreach ([
                'tanggal_terbit'    => 'Tanggal Terbit *',
                'tanggal_mulai'     => 'Tanggal Mulai *',
                'target_pengiriman' => 'Target Pengiriman *',
                'tanggal_pengingat' => 'Tanggal Pengingat *',
            ] as $nama => $label)
                <div>
                    <label for="{{ $nama }}" class="mb-2 block text-[12px] font-medium leading-4 text-gray-800">{{ $label }}</label>
                    <input id="{{ $nama }}" name="{{ $nama }}" type="date" required value="{{ $form[$nama] }}" class="{{ $kelasIsian }}">
                </div>
            @endforeach
        </div>
    </section>

    <!-- Tombol Batal (kembali ke Detail Kontrak) dan Kirim Kontrak -->
    <div class="mt-4 flex justify-end gap-3">
        <a href="{{ route('ppbj.kontrak.show', $kontrak['id']) }}" class="flex h-10 items-center rounded-lg border border-gray-200 bg-white px-6 text-[13px] font-semibold text-gray-800 transition hover:bg-gray-50">Batal</a>
        <button type="submit" class="h-10 rounded-lg bg-tosca-900 px-6 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Kirim Kontrak</button>
    </div>
</form>

@push('skrip')
<script>
    // Selama action form masih kosong (route belum dibuat), pengiriman dicegah agar tidak terjadi galat 405
    document.querySelector('main form').addEventListener('submit', (e) => {
        if (!e.currentTarget.getAttribute('action')) e.preventDefault();
    });
</script>
@endpush

@endsection
