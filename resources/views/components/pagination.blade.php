{{--
    Navigasi halaman tabel yang dipakai BERSAMA oleh semua halaman daftar (Super Admin, dashboard PPBJ, dst).
    Tulis sekali di sini, lalu pasang di bawah tabel: <x-pagination :paginasi="$paginasi" />

    Props:
      paginasi : ['halaman' => halaman aktif, 'total_halaman' => int, 'dari' => int, 'sampai' => int, 'total' => int]
      satuan   : kata benda di keterangan "Menampilkan 1-6 dari 18 data" (default "data")
    Tautan memakai request()->fullUrlWithQuery(['page' => n]) sehingga pencarian dan filter (?q=, ?status=) tetap terbawa.
--}}
@props(['paginasi', 'satuan' => 'data'])

<!-- Footer tabel: keterangan jumlah data di kiri, navigasi halaman di kanan -->
<div class="flex flex-wrap items-center justify-between gap-3 px-5 py-[20px] text-[13px] text-gray-500">
    <p>Menampilkan {{ $paginasi['dari'] }}-{{ $paginasi['sampai'] }} dari {{ $paginasi['total'] }} {{ $satuan }}</p>

    <nav class="flex items-center gap-2 text-[12px]" aria-label="Navigasi halaman">
        @if ($paginasi['halaman'] > 1)
            <a href="{{ request()->fullUrlWithQuery(['page' => $paginasi['halaman'] - 1]) }}" class="flex h-[27px] items-center rounded-md border border-gray-200 px-3 font-medium text-gray-700 hover:bg-gray-50">Sebelumnya</a>
        @else
            <span class="flex h-[27px] items-center rounded-md border border-gray-200 px-3 text-gray-400">Sebelumnya</span>
        @endif

        @php
            // Jendela 3 nomor halaman yang mengikuti halaman aktif; titik-titik muncul jika masih ada halaman setelahnya
            $awal = max(1, min($paginasi['halaman'] - 1, $paginasi['total_halaman'] - 2));
            $akhir = min($awal + 2, $paginasi['total_halaman']);
        @endphp
        @for ($nomor = $awal; $nomor <= $akhir; $nomor++)
            <a href="{{ request()->fullUrlWithQuery(['page' => $nomor]) }}"
               class="flex h-[27px] w-[27px] items-center justify-center rounded-md border font-semibold
                      {{ $nomor === $paginasi['halaman'] ? 'border-tosca-900 bg-tosca-900 text-white' : 'border-gray-200 text-gray-700 hover:bg-gray-50' }}">{{ $nomor }}</a>
        @endfor

        @if ($akhir < $paginasi['total_halaman'])
            <span class="text-gray-400">...</span>
        @endif

        @if ($paginasi['halaman'] < $paginasi['total_halaman'])
            <a href="{{ request()->fullUrlWithQuery(['page' => $paginasi['halaman'] + 1]) }}" class="flex h-[27px] items-center rounded-md border border-gray-200 px-3 font-semibold text-gray-800 hover:bg-gray-50">Berikutnya</a>
        @else
            <span class="flex h-[27px] items-center rounded-md border border-gray-200 px-3 text-gray-400">Berikutnya</span>
        @endif
    </nav>
</div>
