{{--
    Satu baris dokumen dengan tautan "Lihat / Unduh" (ikon berkas bercentang, judul tebal, keterangan kecil).
    Props:
      judul      : jenis dokumen (mis. "Invoice", "Kontrak / SPK")
      keterangan : nama berkas - tipe - ukuran - tanggal (mis. "Invoice_GSA_0145.pdf · PDF · 1,2 MB · 28 Okt 2026")
      href       : alamat lihat/unduh. TODO Backend: arahkan ke route lihat/unduh dokumen
--}}
@props(['judul', 'keterangan', 'href' => '#'])

<li class="flex items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-[10px]">
    <svg class="h-[17px] w-[17px] shrink-0 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 10.5l1.5 1.5 3-3M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
    <div class="min-w-0 flex-1">
        <p class="text-[13px] font-bold leading-4">{{ $judul }}</p>
        <p class="mt-[3px] truncate text-[11px] leading-[14px] text-gray-500">{{ $keterangan }}</p>
    </div>
    <a href="{{ $href }}" download class="shrink-0 text-[11px] font-bold text-tosca-400 transition hover:text-tosca-600">Lihat / Unduh</a>
</li>
