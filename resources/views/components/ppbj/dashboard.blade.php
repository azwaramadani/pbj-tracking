{{--
    Kerangka dashboard PPBJ yang dipakai BERSAMA oleh tiga dashboard: Dashboard PPBJ, Kontrak & SPK, dan Pembayaran.
    Backend cukup menukar DATA (kartu ringkasan, kolom tabel, peta status) - HTML-nya tidak perlu ditulis ulang.

    Props:
      ringkasan  : kartu statistik, tiap item ['jumlah' => int, 'label' => string] (baris baru "\n" = pemisah baris teks); 3, 4, atau 5 kartu
      judulTabel : judul kartu tabel (mis. "Daftar Permohonan")
      kolom      : definisi kolom tabel, tiap item ['label' => teks kepala, 'tipe' => surat|jenis|spk|teks|status|pic|tanggal, 'kunci' => kunci data,
                   'lebar' => kelas lebar <col> (kosong = mengambil sisa ruang), 'huruf' => false bila kepala kolom tidak dikapitalkan]
                   Kolom "Aksi" (tombol Detail) selalu ditambahkan di kolom terakhir
      baris      : data baris halaman ini (Collection)
      pilStatus  : peta status => [label, kelas warna]; pilJenis: peta jenis => [label, kelas warna] (opsional)
      total, paginasi, filter : angka pil "Total", data paginasi, dan nilai pencarian/filter aktif
      urlHalaman : alamat form pencarian + filter (GET); ruteDetail: nama route tombol Detail (menerima id baris)
--}}
@props(['ringkasan', 'judulTabel', 'kolom', 'baris', 'pilStatus', 'pilJenis' => [], 'total', 'paginasi', 'filter', 'urlHalaman', 'ruteDetail'])

@php
    // Jumlah kolom grid kartu mengikuti jumlah kartu (3, 4, atau 5)
    $kelasGridKartu = match (count($ringkasan)) {
        3       => 'md:grid-cols-3',
        4       => 'sm:grid-cols-2 xl:grid-cols-4',
        default => 'sm:grid-cols-2 xl:grid-cols-5',
    };
@endphp

<!-- Pembungkus konten Dashboard: margin kanan tambahan agar kartu sejajar dengan mockup -->
<div class="xl:pr-4">

<!-- ===================== GRID KARTU RINGKASAN (angka besar + label + gelombang tosca) ===================== -->
<section class="grid grid-cols-1 gap-4 {{ $kelasGridKartu }}">
    @foreach ($ringkasan as $kartu)
        <div class="relative grid h-[96px] grid-cols-[84px_1fr] items-center overflow-hidden rounded-xl border border-gray-100 bg-white shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <p class="pl-[26px] text-[48px] font-bold leading-none">{{ $kartu['jumlah'] }}</p>
            <p class="relative z-10 whitespace-pre-line pl-3 text-[16px] font-medium leading-[20px] text-gray-600">{{ $kartu['label'] }}</p>

            <!-- Hiasan gelombang tosca di pojok kanan bawah kartu (murni dekorasi) -->
            <svg class="pointer-events-none absolute bottom-0 right-0 h-[44px] w-[118px]" viewBox="0 0 118 44" fill="none" aria-hidden="true">
                <path d="M0 44 C40 42 72 35 118 0 L118 44 Z" fill="#57818d"/>
            </svg>
        </div>
    @endforeach
</section>

<!-- ===================== KARTU TABEL ===================== -->
<section class="mt-6 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-[0_1px_3px_rgba(15,23,42,0.06)]">

    <!-- Baris judul kartu: judul + pil total di kiri; pencarian dan filter di kanan -->
    <div class="flex flex-wrap items-center justify-between gap-3 p-5">
        <div class="flex items-center gap-5">
            <h2 class="text-[20px] font-bold leading-7">{{ $judulTabel }}</h2>
            <!-- TODO Backend: isi angka dengan jumlah data yang ditugaskan kepada PPBJ -->
            <span class="rounded-md border border-[#cdd9e3] bg-[#ebf3f9] px-[14px] py-[3px] text-[14px] text-gray-600">Total: {{ $total }} Permohonan</span>
        </div>

        {{-- Form pencarian + filter memakai method GET agar hasilnya bisa di-bookmark; backend membaca parameter "q" (nomor surat) dan "status" --}}
        <form method="GET" action="{{ $urlHalaman }}" class="flex items-center gap-[12px]">
            <div class="relative">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input type="text" name="q" value="{{ $filter['q'] }}" placeholder="Cari nomor surat permohonan..."
                       class="h-[34px] w-[255px] rounded-lg border border-gray-200 bg-white pl-8 pr-2 text-[14px] text-gray-900 placeholder-gray-500 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20">
            </div>

            <!-- Tombol Filter: membuka panel kecil berisi pilihan status (elemen details, tanpa JavaScript) -->
            <details class="relative">
                <summary class="flex h-[34px] cursor-pointer list-none items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-[12px] text-[14px] font-semibold transition hover:bg-gray-50 [&::-webkit-details-marker]:hidden">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                    </svg>
                    Filter
                </summary>
                <div class="absolute right-0 z-20 mt-2 w-[220px] rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                    <label for="filter-status" class="mb-1.5 block text-[12px] font-semibold text-gray-600">Status</label>
                    <select id="filter-status" name="status" class="h-9 w-full rounded-md border border-gray-200 bg-white px-2 text-[13px] outline-none focus:border-tosca-600">
                        <option value="">Semua Status</option>
                        @foreach ($pilStatus as $kunci => [$labelPil])
                            <option value="{{ $kunci }}" @selected($filter['status'] === $kunci)>{{ ucfirst(strtolower($labelPil)) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="mt-3 h-9 w-full rounded-md bg-tosca-900 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Terapkan</button>
                </div>
            </details>
        </form>
    </div>

    <!-- Tabel (dibungkus agar bisa di-scroll horizontal di layar sempit) -->
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1000px] table-fixed text-left">
            <colgroup>
                @foreach ($kolom as $k)
                    <col @class([$k['lebar'] ?? null])>
                @endforeach
                <col class="w-[95px]">
            </colgroup>

            <!-- Kepala tabel: latar tosca gelap dengan teks putih (judul kolom dikirim backend lewat $kolom) -->
            <thead class="bg-tosca-900 text-[14px] font-semibold text-white">
                <tr class="h-[53px]">
                    @foreach ($kolom as $k)
                        <th class="{{ $loop->first ? 'pl-5' : '' }} font-semibold {{ ($k['huruf'] ?? true) ? 'uppercase' : '' }}">{{ $k['label'] }}</th>
                    @endforeach
                    <th class="font-semibold uppercase">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-[14px]">
                <!-- MULAI LOOP: Baris data dari database (satu iterasi = satu baris tabel). TODO Backend: ganti $baris dengan hasil query Eloquent -->
                @foreach ($baris as $b)
                    <tr class="h-[66px] border-b border-gray-100">
                        @foreach ($kolom as $k)
                            @php $nilai = $b[$k['kunci']] ?? ''; @endphp
                            @switch($k['tipe'])
                                @case('surat')
                                    {{-- Ikon dokumen, nomor surat (tebal), dan tanggal surat (kecil, abu-abu) --}}
                                    <td class="pl-[22px]">
                                        <div class="flex items-center gap-[20px]">
                                            <svg class="h-[18px] w-[18px] shrink-0 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            <div class="min-w-0">
                                                <p class="truncate font-bold leading-5">{{ $nilai }}</p>
                                                <p class="text-[12px] leading-4 text-gray-400">{{ $b['tgl_surat'] }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    @break
                                @case('jenis')
                                    {{-- Pil jenis pengadaan (JASA ungu, BARANG biru) --}}
                                    @php [$labelJenis, $kelasJenis] = $pilJenis[$nilai] ?? [strtoupper($nilai), 'bg-[#f1f2f6] text-gray-500']; @endphp
                                    <td><span class="inline-flex h-[25px] w-[80px] items-center justify-center rounded-lg text-[12px] font-bold {{ $kelasJenis }}">{{ $labelJenis }}</span></td>
                                    @break
                                @case('spk')
                                    {{-- Nomor SPK (tebal, warna tosca gelap) --}}
                                    <td class="truncate pr-3 text-[12px] font-bold text-brand-800">{{ $nilai }}</td>
                                    @break
                                @case('status')
                                    {{-- Pil status; warna dan teks dari peta $pilStatus --}}
                                    @php [$labelStatus, $kelasStatus] = $pilStatus[$nilai] ?? [strtoupper($nilai), 'bg-[#f1f2f6] text-gray-500']; @endphp
                                    <td><span class="inline-flex h-[25px] w-[100px] items-center justify-center rounded-lg text-[12px] font-bold {{ $kelasStatus }}">{{ $labelStatus }}</span></td>
                                    @break
                                @case('tanggal')
                                @case('pic')
                                    <td class="truncate pr-3 text-gray-800">{{ $nilai }}</td>
                                    @break
                                @default
                                    <td class="truncate pr-4" title="{{ $nilai }}">{{ $nilai }}</td>
                            @endswitch
                        @endforeach

                        <!-- Kolom Aksi: tombol "Detail" menuju halaman detail (tampilan menyesuaikan status) -->
                        <td>
                            <a href="{{ route($ruteDetail, $b['id']) }}"
                               class="inline-flex h-[34px] w-[75px] items-center justify-center rounded-md bg-tosca-900 text-[14px] font-semibold text-white transition hover:bg-tosca-800">Detail</a>
                        </td>
                    </tr>
                @endforeach
                <!-- AKHIR LOOP: Baris data -->

                <!-- Keadaan kosong: tampil jika pencarian/filter tidak menemukan data -->
                @if ($baris->isEmpty())
                    <tr class="h-[66px] border-b border-gray-100">
                        <td colspan="{{ count($kolom) + 1 }}" class="px-5 text-center text-gray-500">Tidak ada data yang sesuai.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Footer tabel: keterangan jumlah dan navigasi halaman (komponen bersama x-pagination) -->
    <x-pagination :paginasi="$paginasi" satuan="paket pengadaan" />
</section>

</div>
