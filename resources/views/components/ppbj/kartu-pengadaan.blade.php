{{--
    Kartu ringkasan pengadaan di sisi kiri atas halaman detail PPBJ: judul, kode, Unit / Nilai Pengadaan / PPK, lalu satu tombol tautan.
    Dipakai oleh Metode Pengadaan, Unggah Kontrak & SPK, dan Unggah Dokumen Pembayaran.

    Props:
      data         : judul, kode, unit, nilai, ppk
      labelTombol  : teks tombol (mis. "Lihat Permohonan", "Lihat Pengadaan", "Lihat Dokumentasi Penerimaan")
      url          : alamat tujuan tombol
--}}
@props(['data', 'labelTombol', 'url'])

<section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
    <h2 class="text-[18px] font-bold leading-6">{{ $data['judul'] }}</h2>
    <p class="mt-[3px] text-[11px] leading-4 text-gray-500">{{ $data['kode'] }}</p>

    <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-3">
        <div>
            <dt class="text-[12px] leading-4 text-gray-500">Unit</dt>
            <dd class="mt-[5px] text-[14px] leading-5">{{ $data['unit'] }}</dd>
        </div>
        <div>
            <dt class="text-[12px] leading-4 text-gray-500">Nilai Pengadaan</dt>
            <dd class="mt-[5px] text-[14px] leading-5">{{ $data['nilai'] }}</dd>
        </div>
        <div>
            <dt class="text-[12px] leading-4 text-gray-500">PPK</dt>
            <dd class="mt-[5px] text-[14px] leading-5">{{ $data['ppk'] }}</dd>
        </div>
    </dl>

    <a href="{{ $url }}"
       class="mt-4 flex h-[38px] w-full items-center justify-center rounded-lg border border-gray-200 bg-white text-[13px] font-semibold text-brand-800 transition hover:bg-gray-50">{{ $labelTombol }}</a>
</section>
