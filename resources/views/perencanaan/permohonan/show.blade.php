@extends('layouts.perencanaan')

@section('judul_halaman', 'Detail Permohonan')
@section('menu_aktif', 'dashboard')
@section('url_kembali', route('perencanaan.dashboard'))

@section('konten')

{{--
    Detail Permohonan Perencanaan - satu view untuk dua tampilan, ditentukan oleh $permohonan['status']:
      'diproses' / 'tinjauan' : Form Alokasi Anggaran (MAK) dapat diisi + tombol "Keputusan" dan "Pembagian MAK"
      'selesai'               : Lembar Rekomendasi & Aksi (dua memo dapat diunduh) + Form Alokasi Anggaran hanya-baca, tanpa tombol aksi
    Variabel dari Perencanaan\PermohonanController@show:
      $permohonan    : id, status, nomor_surat, unit_pengaju, judul, tujuan, total_anggaran, lampiran[], pengaju{nama,nip,inisial},
                       anggaran{jenis_anggaran,mata_anggaran,jumlah_alokasi}, memo[] (nama, ukuran) - lihat App\Support\Perencanaan\DataPerencanaan
      $jenisAnggaran : daftar pilihan "Jenis Anggaran"
--}}

@php $selesai = $permohonan['status'] === 'selesai'; @endphp

<!-- ===================== BARIS UTAMA: INFORMASI & LAMPIRAN (kiri) + IDENTITAS, REKOMENDASI & ANGGARAN (kanan) ===================== -->
<div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_386px]">

    <!-- ---------- KOLOM KIRI (sama untuk semua status): Informasi Pengadaan + Lampiran Dokumen Pendukung ---------- -->
    <x-wadir2.informasi-pengadaan :permohonan="$permohonan" />

    <!-- ---------- KOLOM KANAN ---------- -->
    <div class="space-y-6">

        <!-- Kartu Identitas Pengaju (sama untuk semua status) -->
        <x-wadir2.identitas-pengaju :pengaju="$permohonan['pengaju']" />

        @if (in_array($permohonan['status'], ['diproses', 'tinjauan'], true))
            {{-- ===== KEADAAN A (status "diproses" / "tinjauan"): Form Alokasi Anggaran dapat diisi ===== --}}
            {{--
                TODO Backend: isi action dengan route penyimpanan alokasi anggaran (mis. route('perencanaan.permohonan.alokasi', $permohonan['id'])).
                Field: jenis_anggaran, mata_anggaran_kegiatan, jumlah_alokasi. Validasi di server: ketiganya wajib, jumlah_alokasi berupa angka.
                Tombol "Keputusan" hanya MEMBUKA modal Konfirmasi Persetujuan (form terpisah di bawah halaman);
                tombol "Pembagian MAK" berupa tautan menuju halaman Split MAK.
            --}}
            <form method="POST" action="">
                @csrf
                <input type="hidden" name="permohonan_id" value="{{ $permohonan['id'] }}">

                <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                    <h2 class="text-[16px] font-bold leading-6 text-brand-800">Form Alokasi Anggaran (MAK)</h2>

                    <label for="jenis_anggaran" class="mb-[6px] mt-[14px] block text-[12px] font-semibold text-gray-800">Jenis Anggaran</label>
                    <!-- Dropdown jenis anggaran dengan panah di kanan. TODO Backend: ganti opsi dengan master jenis anggaran -->
                    <div class="relative">
                        <select id="jenis_anggaran" name="jenis_anggaran" required
                                class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-white pl-3 pr-9 text-[13px] text-gray-900 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20">
                            <!-- MULAI LOOP: Opsi jenis anggaran dari database -->
                            @foreach ($jenisAnggaran as $opsi)
                                <option value="{{ $opsi }}" @selected($opsi === $permohonan['anggaran']['jenis_anggaran'])>{{ $opsi }}</option>
                            @endforeach
                            <!-- AKHIR LOOP: Opsi jenis anggaran -->
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>

                    <label for="mata_anggaran_kegiatan" class="mb-[6px] mt-[14px] block text-[12px] font-semibold text-gray-800">Mata Anggaran Kegiatan (MAK)</label>
                    <!-- Kolom Mata Anggaran dengan ikon kaca pembesar di kanan. TODO Backend: ganti dengan pencarian/pilihan dari master mata anggaran -->
                    <div class="relative">
                        <input id="mata_anggaran_kegiatan" name="mata_anggaran_kegiatan" type="text" required value="{{ $permohonan['anggaran']['mata_anggaran'] }}"
                               class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-3 pr-9 text-[13px] text-gray-900 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20">
                        <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>

                    <label for="jumlah_alokasi" class="mb-[6px] mt-[14px] block text-[12px] font-semibold text-gray-800">Jumlah Alokasi</label>
                    <!-- Kotak awalan "Rp" menyatu dengan kolom angka -->
                    <div class="flex h-10 overflow-hidden rounded-lg border border-gray-200 focus-within:border-tosca-600 focus-within:ring-2 focus-within:ring-tosca-600/20">
                        <span class="flex w-[42px] shrink-0 items-center justify-center bg-[#e9ecef] text-[13px] font-semibold text-gray-700">Rp</span>
                        <input id="jumlah_alokasi" name="jumlah_alokasi" type="text" inputmode="numeric" required value="{{ $permohonan['anggaran']['jumlah_alokasi'] }}"
                               class="min-w-0 flex-1 bg-white px-3 text-[13px] text-gray-900 outline-none">
                    </div>

                    <!-- Tombol aksi: "Keputusan" membuka modal; "Pembagian MAK" menuju halaman Split MAK -->
                    <button type="button" data-buka-modal="modal-konfirmasi"
                            class="mt-[22px] h-10 w-full rounded-lg bg-tosca-900 text-[14px] font-semibold text-white transition hover:bg-tosca-800">Keputusan</button>
                    <a href="{{ route('perencanaan.split-mak.show', $permohonan['id']) }}"
                       class="mt-3 flex h-10 w-full items-center justify-center rounded-lg border border-tosca-900 bg-white text-[14px] font-semibold text-tosca-900 transition hover:bg-[#e8f3f5]">Pembagian MAK</a>
                </section>
            </form>
        @elseif ($selesai)
            {{-- ===== KEADAAN B (status "selesai"): memo sudah terbit, form hanya-baca tanpa tombol aksi ===== --}}

            <!-- Kartu Lembar Rekomendasi & Aksi: dua memo persetujuan (Wadir 2 dan PPK) yang dapat diunduh -->
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="border-b border-gray-200 pb-[14px] text-[16px] font-bold leading-6 text-brand-800">Lembar Rekomendasi &amp; Aksi</h2>

                <ul class="mt-4 space-y-[11px]">
                    <!-- MULAI LOOP: Berkas memo persetujuan dari database (satu iterasi = satu memo). TODO Backend: arahkan tautan unduh ke route unduh memo -->
                    @foreach ($permohonan['memo'] as $memo)
                        <li class="flex min-h-[58px] items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-[11px] py-2">
                            <span class="flex h-[36px] w-[36px] shrink-0 items-center justify-center rounded-lg bg-[#fde2e2] text-[#e5484d]">
                                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="break-words text-[13px] font-bold leading-4">{{ $memo['nama'] }}</p>
                                <p class="text-[11px] leading-4 text-gray-500">{{ $memo['ukuran'] }}</p>
                            </div>
                            <a href="#" download class="flex h-[26px] shrink-0 items-center gap-1.5 rounded-md border border-gray-200 bg-white px-[11px] text-[12px] font-semibold text-gray-900 transition hover:bg-gray-50">
                                <svg class="h-[13px] w-[13px]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Unduh
                            </a>
                        </li>
                    @endforeach
                    <!-- AKHIR LOOP: Berkas memo persetujuan -->
                </ul>
            </section>

            <!-- Kartu Form Alokasi Anggaran (MAK): terisi dan hanya-baca (tanpa form dan tanpa tombol aksi) -->
            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-[16px] font-bold leading-6 text-brand-800">Form Alokasi Anggaran (MAK)</h2>

                <label for="jenis_anggaran" class="mb-[6px] mt-[14px] block text-[12px] font-semibold text-gray-800">Jenis Anggaran</label>
                <div class="relative">
                    <input id="jenis_anggaran" name="jenis_anggaran" type="text" readonly value="{{ $permohonan['anggaran']['jenis_anggaran'] }}"
                           class="h-10 w-full cursor-default rounded-lg border border-gray-200 bg-white pl-3 pr-9 text-[13px] text-gray-900 outline-none">
                    <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>

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
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Persetujuan (komponen bersama; hanya diperlukan saat tombol "Keputusan" tampil) -->
@unless ($selesai)
    <x-wadir2.konfirmasi-persetujuan :record-id="$permohonan['id']" field-name="permohonan_id" />
@endunless

@endsection
