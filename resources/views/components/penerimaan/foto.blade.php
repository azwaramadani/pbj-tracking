{{--
    Satu foto fisik barang (kotak 90px dengan label di pojok kiri bawah). Dipakai oleh halaman Dokumentasi Penerimaan
    untuk foto tersimpan (di-render server) dan sebagai cetakan foto baru yang dibuat skrip saat pengguna menambah foto.

    Props:
      label        : teks label foto (mis. "Kemasan Box")
      url          : alamat foto; null = tampil latar pengganti bergradasi
      fotoId       : id foto tersimpan (dikirim lewat hapus_foto[] saat dihapus); null untuk foto yang baru ditambahkan
      bisaDipilih  : true pada keadaan "dijalankan" (foto dapat diklik untuk ditandai sebelum "Hapus Foto")
--}}
@props(['label' => '', 'url' => null, 'fotoId' => null, 'bisaDipilih' => false])

<figure data-foto @if ($fotoId) data-foto-id="{{ $fotoId }}" @endif
        class="group relative h-[90px] overflow-hidden rounded-lg bg-gradient-to-br from-[#d9c9b0] via-[#b9a58a] to-[#8f7c63] data-[terpilih]:ring-[3px] data-[terpilih]:ring-tosca-400 {{ $bisaDipilih ? 'cursor-pointer' : '' }}">
    <img data-gambar @if ($url) src="{{ $url }}" @endif alt="{{ $label }}" class="h-full w-full object-cover {{ $url ? '' : 'hidden' }}">
    <figcaption data-label class="absolute bottom-1.5 left-1.5 max-w-[85%] truncate rounded bg-black/55 px-1.5 py-[2px] text-[9px] font-medium text-white">{{ $label }}</figcaption>

    <!-- Tanda centang: tampil hanya saat foto dipilih (untuk dihapus) -->
    @if ($bisaDipilih)
        <span class="absolute right-1.5 top-1.5 hidden h-5 w-5 items-center justify-center rounded-full bg-tosca-400 text-white group-data-[terpilih]:flex">
            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12.5l4.5 4.5L19 7.5" /></svg>
        </span>
    @endif
</figure>
