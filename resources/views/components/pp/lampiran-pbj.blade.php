{{--
    Kartu "Lampiran Pendukung PBJ" (PP / PPK): satu baris per berkas dengan tombol Unduh.

    Props:
      lampiran : daftar berkas, tiap item ['nama' => string, 'ukuran' => string]
--}}
@props(['lampiran'])

<section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
    <h2 class="flex items-center gap-2 border-b border-gray-200 pb-[14px] text-[17px] font-bold leading-6">
        <svg class="h-5 w-5 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
        </svg>
        Lampiran Pendukung PBJ
    </h2>

    <ul class="mt-4 space-y-[11px]">
        <!-- MULAI LOOP: Berkas lampiran pengadaan dari database (satu iterasi = satu berkas) -->
        @foreach ($lampiran as $berkas)
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
