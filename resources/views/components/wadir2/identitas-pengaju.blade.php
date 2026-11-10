{{--
    Kartu "Identitas Pengaju": avatar inisial, nama, dan NIP pengaju permohonan.
    Props:
      pengaju : nama, nip, inisial
--}}
@props(['pengaju'])

<section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
    <h2 class="border-b border-gray-200 pb-[14px] text-[16px] font-bold leading-6 text-brand-800">Identitas Pengaju</h2>

    <div class="mt-4 flex items-center gap-[14px]">
        <!-- TODO Backend: tampilkan foto pengaju bila ada; sementara memakai lingkaran berisi inisial -->
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#cce9e4] text-[14px] font-bold text-brand-800">{{ $pengaju['inisial'] }}</span>
        <div class="min-w-0">
            <p class="text-[14px] font-bold leading-5">{{ $pengaju['nama'] }}</p>
            <p class="text-[12px] leading-4 text-gray-500">NIP. {{ $pengaju['nip'] }}</p>
        </div>
    </div>
</section>
