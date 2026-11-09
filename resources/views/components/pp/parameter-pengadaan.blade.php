{{--
    Kartu "Parameter Pengadaan" (PP / PPK): vendor, nilai HPS, jenis pengadaan, dan kode MAK.

    Props:
      pengadaan : vendor, nilai_hps, jenis, kode_mak
--}}
@props(['pengadaan'])

<section class="rounded-2xl border border-gray-100 bg-white px-6 pb-[22px] pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
    <div class="flex items-center justify-between border-b border-gray-200 pb-[14px]">
        <h2 class="flex items-center gap-2 text-[17px] font-bold leading-6">
            <svg class="h-5 w-5 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Parameter Pengadaan
        </h2>
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
