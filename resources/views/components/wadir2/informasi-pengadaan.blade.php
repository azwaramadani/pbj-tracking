{{--
    Kolom kiri Detail Permohonan Wadir 2 / MAK / Tracking PPK: kartu "Informasi Pengadaan" + kartu "Lampiran Dokumen Pendukung".

    Props:
      permohonan : nomor_surat, unit_pengaju, judul, tujuan, total_anggaran, lampiran[] (nama, ukuran)
--}}
@props(['permohonan'])

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
                <li class="flex h-[58px] items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-[11px]">
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
