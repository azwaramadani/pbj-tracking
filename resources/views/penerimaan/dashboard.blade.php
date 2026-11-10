@extends('layouts.penerimaan')

@section('judul_halaman', 'Dashboard Penerimaan')
@section('menu_aktif', 'dashboard')

@section('konten')

{{--
    Dashboard Penerimaan - tiga kartu ringkasan + tabel "Daftar Penerimaan".
    Variabel dari Penerimaan\DashboardController@index:
      $ringkasan       : 3 kartu statistik, tiap item ['jumlah' => int, 'label' => string] (baris baru "\n" = pemisah baris teks)
      $penerimaan      : penerimaan pada halaman ini; tiap item memuat id, no_surat, tanggal_surat, nomor_spk, judul, status (dijalankan|selesai), tgl_kirim
      $totalPenerimaan : angka pada pil "Total: n Permohonan"
      $paginasi        : halaman (aktif), total_halaman, dari, sampai, total
      $filter          : nilai pencarian (q) dan filter status yang sedang aktif
--}}

@php
    /* Peta status => [label, kelas warna pil]. Hanya dua lencana: DIJALAN (kuning) dan SELESAI (hijau) */
    $pilStatus = [
        'dijalankan' => ['DIJALAN', 'bg-[#fef3c7] text-[#f0a30a]'],
        'selesai'    => ['SELESAI', 'bg-[#d9f5e5] text-[#1fa35c]'],
    ];
@endphp

<!-- Pembungkus konten Dashboard: margin kanan tambahan agar kartu sejajar dengan mockup -->
<div class="xl:pr-4">

<!-- ===================== GRID KARTU RINGKASAN (3 kolom: Menunggu Penerimaan, SPK Aktif, Penerimaan selesai) ===================== -->
<section class="grid grid-cols-1 gap-4 md:grid-cols-3">
    @foreach ($ringkasan as $kartu)
        <!-- Satu kartu statistik: angka di kiri, label di tengah-kanan, hiasan gelombang di pojok kanan bawah -->
        <div class="relative grid h-[88px] grid-cols-[110px_1fr] items-center overflow-hidden rounded-xl border border-gray-100 bg-white shadow-[0_1px_3px_rgba(15,23,42,0.06)] xl:grid-cols-[150px_1fr]">
            <p class="pl-[30px] text-[48px] font-bold leading-none">{{ $kartu['jumlah'] }}</p>
            <p class="relative z-10 whitespace-pre-line text-[16px] font-medium leading-[20px] text-gray-600">{{ $kartu['label'] }}</p>

            <!-- Hiasan gelombang tosca di pojok kanan bawah kartu (murni dekorasi) -->
            <svg class="pointer-events-none absolute bottom-0 right-0 h-[42px] w-[118px]" viewBox="0 0 118 42" fill="none" aria-hidden="true">
                <path d="M0 42 C40 40 72 34 118 0 L118 42 Z" fill="#57818d"/>
            </svg>
        </div>
    @endforeach
</section>

<!-- ===================== KARTU TABEL "DAFTAR PENERIMAAN" ===================== -->
<section class="mt-6 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-[0_1px_3px_rgba(15,23,42,0.06)]">

    <!-- Baris judul kartu: judul + pil total di kiri; pencarian dan filter di kanan -->
    <div class="flex flex-wrap items-center justify-between gap-3 p-5">
        <div class="flex items-center gap-5">
            <h2 class="text-[20px] font-bold leading-7">Daftar Penerimaan</h2>
            <!-- TODO Backend: isi angka dengan jumlah penerimaan yang ditugaskan kepada role Penerimaan -->
            <span class="rounded-md border border-[#cdd9e3] bg-[#ebf3f9] px-[14px] py-[3px] text-[14px] text-gray-600">Total: {{ $totalPenerimaan }} Permohonan</span>
        </div>

        {{--
            Form pencarian + filter memakai method GET agar hasilnya bisa di-bookmark dan tidak butuh token CSRF.
            Backend membaca parameter "q" (nomor surat) dan "status" lalu menyaring query daftar penerimaan.
        --}}
        <form method="GET" action="{{ route('penerimaan.dashboard') }}" class="flex items-center gap-[12px]">

            <!-- Kolom pencarian nomor surat dengan ikon kaca pembesar -->
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

    <!-- Tabel daftar penerimaan (dibungkus agar bisa di-scroll horizontal di layar sempit) -->
    <div class="overflow-x-auto">
        <table class="w-full min-w-[960px] table-fixed text-left">
            <!-- Lebar kolom tetap agar sejajar dengan mockup -->
            <colgroup>
                <col class="w-[285px]">
                <col class="w-[215px]">
                <col>
                <col class="w-[120px]">
                <col class="w-[125px]">
                <col class="w-[95px]">
            </colgroup>

            <!-- Kepala tabel: latar tosca gelap dengan teks putih ("Nomor SPK" memakai huruf sesuai mockup) -->
            <thead class="bg-tosca-900 text-[14px] font-semibold text-white">
                <tr class="h-[53px]">
                    <th class="pl-5 font-semibold uppercase">No Surat</th>
                    <th class="font-semibold">Nomor SPK</th>
                    <th class="font-semibold uppercase">Judul Pengadaan</th>
                    <th class="font-semibold uppercase">Status</th>
                    <th class="font-semibold uppercase">Tanggal</th>
                    <th class="font-semibold uppercase">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-[14px]">
                <!-- MULAI LOOP: Baris penerimaan dari database (satu iterasi = satu baris tabel). TODO Backend: ganti $penerimaan dengan hasil query Eloquent -->
                @foreach ($penerimaan as $baris)
                    @php [$labelStatus, $kelasStatus] = $pilStatus[$baris['status']] ?? $pilStatus['dijalankan']; @endphp
                    <tr class="h-[59px] border-b border-gray-100">

                        <!-- Kolom 1: ikon dokumen, nomor surat (tebal), dan tanggal surat (kecil, abu-abu) -->
                        <td class="pl-[22px]">
                            <div class="flex items-center gap-[20px]">
                                <svg class="h-[18px] w-[18px] shrink-0 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <div class="min-w-0">
                                    <p class="truncate font-bold leading-5">{{ $baris['no_surat'] }}</p>
                                    <p class="text-[12px] leading-4 text-gray-400">{{ $baris['tanggal_surat'] }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Kolom 2: nomor SPK (tebal, warna tosca gelap) -->
                        <td class="truncate pr-3 text-[12px] font-bold text-brand-800">{{ $baris['nomor_spk'] }}</td>

                        <!-- Kolom 3: judul pengadaan -->
                        <td class="truncate pr-4">{{ $baris['judul'] }}</td>

                        <!-- Kolom 4: pil status; hanya DIJALAN (kuning) atau SELESAI (hijau) -->
                        <td>
                            <span class="inline-flex h-[25px] w-[88px] items-center justify-center rounded-lg text-[12px] font-bold {{ $kelasStatus }}">{{ $labelStatus }}</span>
                        </td>

                        <!-- Kolom 5: tanggal -->
                        <td class="text-gray-800">{{ $baris['tgl_kirim'] }}</td>

                        <!-- Kolom 6: tombol "Detail" menuju Dokumentasi Penerimaan (tampilan menyesuaikan status) -->
                        <td>
                            <a href="{{ route('penerimaan.dokumentasi.show', $baris['id']) }}"
                               class="inline-flex h-[34px] w-[75px] items-center justify-center rounded-md bg-tosca-900 text-[14px] font-semibold text-white transition hover:bg-tosca-800">Detail</a>
                        </td>
                    </tr>
                @endforeach
                <!-- AKHIR LOOP: Baris penerimaan -->

                <!-- Keadaan kosong: tampil jika pencarian/filter tidak menemukan data -->
                @if ($penerimaan->isEmpty())
                    <tr class="h-[59px] border-b border-gray-100">
                        <td colspan="6" class="px-5 text-center text-gray-500">Tidak ada penerimaan yang sesuai.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Footer tabel: keterangan jumlah data di kiri, navigasi halaman di kanan -->
    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-[20px] text-[13px] text-gray-500">
        <!-- TODO Backend: isi dari hasil paginasi (nomor awal, nomor akhir, total data) -->
        <p>Menampilkan {{ $paginasi['dari'] }}-{{ $paginasi['sampai'] }} dari {{ $paginasi['total'] }} paket pengadaan</p>

        <!-- Navigasi halaman: tombol Sebelumnya, nomor halaman, titik-titik, tombol Berikutnya (tautan GET yang mempertahankan filter) -->
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
                {{-- Halaman aktif diberi latar tosca gelap --}}
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
</section>

</div>

@endsection
