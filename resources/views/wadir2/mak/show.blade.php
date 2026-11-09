@extends('layouts.'.$prefixRute)

@section('judul_halaman', 'Detail Permohonan')
@section('menu_aktif', 'permohonan-mak')
@section('url_kembali', route($prefixRute.'.mak.index'))

@section('konten')

{{--
    Detail Permohonan MAK (Wadir 2) - satu view untuk dua tampilan, ditentukan oleh $permohonan['status']:
      'tinjau'  : Wadir 2 masih harus bertindak (Terbitkan Memo + isi Form Alokasi Anggaran)
      'selesai' : memo sudah terbit (unduh memo, Form Alokasi Anggaran terisi hanya-baca, kartu status PPK)
    Variabel dari Wadir2\MakController@show:
      $permohonan : id, status, nomor_surat, unit_pengaju, judul, tujuan, total_anggaran, lampiran[], pengaju{nama,nip,inisial},
                    anggaran{mata_anggaran,jumlah_alokasi}, memo{nama,ukuran}|null, ppk{judul,subjudul,status,penanggung_jawab,waktu}
                    - lihat App\Support\Wadir2\DataMak
--}}

@php $selesai = $permohonan['status'] === 'selesai'; @endphp

<!-- ===================== BARIS UTAMA: INFORMASI & LAMPIRAN (kiri) + AKSI & ANGGARAN (kanan) ===================== -->
<div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_386px]">

    <!-- ---------- KOLOM KIRI (sama untuk kedua status) ---------- -->
    <div class="space-y-6">

        <!-- Kartu Informasi Pengadaan: judul kartu, garis pemisah, lalu pasangan label-isi -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-[23px] pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="border-b border-gray-200 pb-[18px] text-[16px] font-bold leading-6 text-brand-800">Informasi Pengadaan</h2>

            <dl class="mt-[18px] grid grid-cols-1 gap-x-6 gap-y-[18px] md:grid-cols-[323px_1fr]">
                <div>
                    <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">Nomor Surat Pernyataan</dt>
                    <dd class="mt-[3px] text-[14px] leading-5 text-gray-900">{{ $permohonan['nomor_surat'] }}</dd>
                </div>
                <div>
                    <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">Unit Pengaju</dt>
                    <dd class="mt-[3px] text-[14px] font-medium leading-5 text-gray-900">{{ $permohonan['unit_pengaju'] }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">Judul Pengajuan</dt>
                    <dd class="mt-[3px] text-[14px] leading-5 text-gray-900">{{ $permohonan['judul'] }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">Tujuan Pengadaan</dt>
                    <dd class="mt-[3px] text-[14px] leading-[22px] text-gray-900">{{ $permohonan['tujuan'] }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">Total Estimasi Anggaran</dt>
                    <!-- TODO Backend: format angka rupiah dari kolom nilai anggaran -->
                    <dd class="mt-[3px] text-[20px] font-bold leading-7 text-tosca-400">{{ $permohonan['total_anggaran'] }}</dd>
                </div>
            </dl>
        </section>

        <!-- Kartu Lampiran Dokumen Pendukung: satu baris per berkas dengan tombol Unduh -->
        <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6 text-brand-800">Lampiran Dokumen Pendukung</h2>

            <ul class="mt-[17px] space-y-[11px]">
                <!-- MULAI LOOP: Berkas lampiran permohonan dari database (satu iterasi = satu berkas) -->
                @foreach ($permohonan['lampiran'] as $berkas)
                    <li class="flex h-[58px] items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] pl-[11px] pr-[11px]">
                        <!-- Ikon berkas dalam kotak merah muda -->
                        <span class="flex h-[36px] w-[36px] shrink-0 items-center justify-center rounded-lg bg-[#fde2e2] text-[#e5484d]">
                            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[13px] font-bold leading-4">{{ $berkas['nama'] }}</p>
                            <p class="text-[11px] leading-4 text-gray-500">{{ $berkas['ukuran'] }}</p>
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

    <!-- ---------- KOLOM KANAN (isi berbeda menurut status) ---------- -->
    <div class="space-y-6">

        <!-- Kartu Identitas Pengaju (sama untuk kedua status) -->
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

        @if (! $selesai)
            {{-- ===== KEADAAN A (status "tinjau"): Wadir 2 perlu bertindak ===== --}}
            {{--
                TODO Backend: isi action dengan route penerbitan memo (mis. route('wadir2.mak.terbitkan-memo', $permohonan['id'])).
                Satu form (multipart) membungkus Form Alokasi Anggaran dan modal "Memo Persetujuan", sehingga berkas memo dan data MAK
                terkirim bersamaan saat "Kirim Lembar Persetujuan" ditekan. Field: mata_anggaran_kegiatan, jumlah_alokasi, memo_persetujuan (file).
                Validasi di server: mata_anggaran_kegiatan wajib diisi, jumlah_alokasi harus berupa angka, memo_persetujuan wajib (pdf/xlsx/jpg, maks 10 MB).
            --}}
            <form id="form-mak" method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="permohonan_id" value="{{ $permohonan['id'] }}">

                <!-- Kartu Lembar Rekomendasi & Aksi: tombol "Terbitkan Memo" membuka modal Memo Persetujuan (tidak langsung mengirim form) -->
                <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                    <h2 class="border-b border-gray-200 pb-[14px] text-[16px] font-bold leading-6 text-brand-800">Lembar Rekomendasi &amp; Aksi</h2>
                    <button type="button" data-buka-modal="modal-memo" class="mt-4 h-[35px] w-full rounded-lg bg-tosca-900 text-[14px] font-semibold text-white transition hover:bg-tosca-800">Terbitkan Memo</button>
                </section>

                <!-- Kartu Form Alokasi Anggaran (MAK): dapat diisi -->
                <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                    <h2 class="text-[16px] font-bold leading-6 text-brand-800">Form Alokasi Anggaran (MAK)</h2>

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
                </section>

                <!-- Modal Memo Persetujuan (komponen, disembunyikan secara default; berada di dalam form agar berkas ikut terkirim) -->
                <x-wadir2.modal-memo />
            </form>
        @else
            {{-- ===== KEADAAN B (status "selesai"): memo sudah terbit ===== --}}

            <!-- Kartu Keputusan Persetujuan (1): memo persetujuan yang dapat diunduh -->
            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-[16px] font-bold leading-6 text-brand-800">Keputusan Persetujuan</h2>
                <p class="mt-[10px] text-[13px] leading-[18px] text-gray-500">Lakukan peninjauan secara teliti pada lampiran rincian anggaran sebelum mengambil keputusan.</p>

                <!-- Berkas memo: ikon, nama, ukuran, dan tombol Unduh. TODO Backend: arahkan ke route unduh memo yang sudah diterbitkan -->
                <div class="mt-[14px] flex h-[58px] items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-[11px]">
                    <span class="flex h-[36px] w-[36px] shrink-0 items-center justify-center rounded-lg bg-[#fde2e2] text-[#e5484d]">
                        <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-bold leading-4">{{ $permohonan['memo']['nama'] }}</p>
                        <p class="text-[11px] leading-4 text-gray-500">{{ $permohonan['memo']['ukuran'] }}</p>
                    </div>
                    <a href="#" download class="flex h-[26px] items-center gap-1.5 rounded-md border border-gray-200 bg-white px-[11px] text-[12px] font-semibold text-gray-900 transition hover:bg-gray-50">
                        <svg class="h-[13px] w-[13px]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Unduh
                    </a>
                </div>
            </section>

            <!-- Kartu Form Alokasi Anggaran (MAK): terisi dan hanya-baca karena memo sudah terbit -->
            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-[16px] font-bold leading-6 text-brand-800">Form Alokasi Anggaran (MAK)</h2>

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

            <!-- Kartu Keputusan Persetujuan (2): status persetujuan PPK memakai komponen bersama x-unit.kartu-persetujuan -->
            <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-[16px] font-bold leading-6 text-brand-800">Keputusan Persetujuan</h2>

                <div class="mt-[14px]">
                    <x-unit.kartu-persetujuan
                        :judul="$permohonan['ppk']['judul']"
                        :subjudul="$permohonan['ppk']['subjudul']"
                        :status="$permohonan['ppk']['status']"
                        :penanggung-jawab="$permohonan['ppk']['penanggung_jawab']"
                        :waktu="$permohonan['ppk']['waktu']"
                        kelas-kolom="grid-cols-[133px_1fr]" />
                </div>
            </section>
        @endif
    </div>
</div>

@endsection
