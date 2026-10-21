@extends('layouts.unit')

@section('judul_halaman', 'Permohonan')
@section('menu_aktif', 'permohonan')

@section('konten')

@php
    /*
     * Variabel dari PermohonanController@index:
     *   $permohonan  = paginator berisi baris halaman aktif
     *   $jumlahTab   = jumlah data per status (kunci: semua, draft, menunggu, diproses, selesai)
     *   $statusAktif = kunci tab yang sedang dipilih
     *   $kataKunci   = isi kolom pencarian
     */
    $tabStatus = [
        'semua'    => 'Semua',
        'draft'    => 'Draft',
        'menunggu' => 'Menunggu',
        'diproses' => 'Diproses',
        'selesai'  => 'Selesai',
    ];

    /* Peta status => [warna badge, warna titik] */
    $warnaStatus = [
        'diproses' => ['bg-[#eff6ff] text-[#2f80ed]', 'bg-[#2f80ed]'],
        'menunggu' => ['bg-[#fff6e6] text-[#d87705]', 'bg-[#f49e0a]'],
        'selesai'  => ['bg-[#ebfdf4] text-[#1f9d5b]', 'bg-[#26ae60]'],
        'draft'    => ['bg-[#f2f4f6] text-gray-500',  'bg-gray-400'],
    ];
@endphp

<!-- ===================== JUDUL HALAMAN + TOMBOL BUAT PERMOHONAN BARU ===================== -->
<div class="flex flex-wrap items-start justify-between gap-4">
    <div>
        <h2 class="text-[28px] font-bold leading-tight">Daftar Permohonan Saya</h2>
        <p class="mt-1 text-[14px] text-gray-500">Kelola dan pantau seluruh berkas permohonan logistik Anda</p>
    </div>

    <!-- Tombol utama: membuka formulir usulan pengadaan baru -->
    <a href="{{ route('unit.permohonan.create') }}"
       class="flex h-10 items-center rounded-lg bg-brand-800 px-5 text-[14px] font-semibold text-white transition hover:bg-brand-700">
        Buat Permohonan Baru
    </a>
</div>

<!-- ===================== BARIS TAB FILTER + PENCARIAN ===================== -->
<div class="mt-6 flex flex-wrap items-center justify-between gap-4">

    <!-- Tab filter status: tiap tab adalah tautan ?status=..., kata kunci pencarian ikut dipertahankan -->
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($tabStatus as $kunci => $label)
            @php $aktif = $kunci === $statusAktif; @endphp
            <a href="{{ route('unit.permohonan.index', array_filter(['status' => $kunci === 'semua' ? null : $kunci, 'q' => $kataKunci])) }}"
               class="flex h-[38px] items-center gap-2.5 rounded-lg border px-4 text-[14px] font-semibold transition
                      {{ $aktif ? 'border-brand-800 bg-brand-800 text-white' : 'border-gray-200 bg-white text-gray-900 hover:bg-gray-50' }}">
                {{ $label }}
                <!-- Lencana jumlah: hijau-teal pada tab aktif, abu-abu pada tab lain -->
                <span class="flex h-[18px] min-w-[28px] items-center justify-center rounded-full px-1.5 text-[12px] font-semibold
                             {{ $aktif ? 'bg-accent text-white' : 'bg-gray-100 text-gray-500' }}">
                    {{ $jumlahTab[$kunci] }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Kolom pencarian: dikirim lewat GET (tekan Enter), status tab ikut dibawa sebagai input tersembunyi -->
    <form method="GET" action="{{ route('unit.permohonan.index') }}" class="relative w-full sm:w-[319px]">
        @if ($statusAktif !== 'semua')
            <input type="hidden" name="status" value="{{ $statusAktif }}">
        @endif
        <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <input type="text" name="q" value="{{ $kataKunci }}" placeholder="Cari nomor permohonan atau judul..."
               class="h-[38px] w-full rounded-lg border border-gray-200 bg-white pl-10 pr-3 text-[14px] text-gray-900 placeholder-gray-500 outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/20">
    </form>
</div>

<!-- ===================== KARTU TABEL DAFTAR PERMOHONAN ===================== -->
<section class="mt-8 overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[960px] table-fixed text-left">
            <!-- Lebar kolom tetap agar sejajar dengan mockup -->
            <colgroup>
                <col class="w-[203px]">
                <col class="w-[296px]">
                <col class="w-[144px]">
                <col class="w-[165px]">
                <col class="w-[162px]">
                <col class="w-[128px]">
            </colgroup>

            <!-- Kepala tabel: judul kolom huruf kapital -->
            <thead class="bg-[#f2f4f6] text-[13px] font-semibold uppercase text-gray-500">
                <tr class="h-[47px]">
                    <th class="px-6 font-semibold">No Surat</th>
                    <th class="pl-6 pr-2 font-semibold">Judul Permohonan</th>
                    <th class="px-6 font-semibold">Status</th>
                    <th class="px-6 font-semibold">Tanggal Kirim</th>
                    <th class="whitespace-nowrap pl-6 pr-2 font-semibold">Terakhir Update</th>
                    <th class="px-6 text-center font-semibold">Aksi</th>
                </tr>
            </thead>

            <!-- Isi tabel: satu baris per permohonan pada halaman aktif -->
            <tbody class="text-[13px]">
                @forelse ($permohonan as $baris)
                    @php [$kelasBadge, $kelasTitik] = $warnaStatus[$baris['status']]; @endphp
                    <tr class="h-[60px] border-b border-[#f2f4f6]">
                        <!-- Nomor surat: tebal berwarna teal gelap, menuju halaman detail -->
                        <td class="px-6 text-[14px] font-semibold text-brand-800">
                            <a href="{{ route('unit.permohonan.show', $baris['id']) }}" class="hover:underline">{{ $baris['no_surat'] }}</a>
                        </td>

                        <!-- Judul permohonan: dipotong dengan "..." jika terlalu panjang -->
                        <td class="truncate pl-6 pr-2 text-[14px]" title="{{ $baris['judul'] }}">{{ $baris['judul'] }}</td>

                        <!-- Badge status dengan titik berwarna di depan teks -->
                        <td class="px-6">
                            <span class="inline-flex h-[22px] items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 text-[12px] font-medium {{ $kelasBadge }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $kelasTitik }}"></span>
                                {{ $baris['label_status'] }}
                            </span>
                        </td>

                        <td class="px-6 text-[14px] text-gray-500">{{ $baris['tgl_kirim'] }}</td>
                        <td class="px-6 text-[14px] text-gray-500">{{ $baris['tgl_update'] }}</td>

                        <!-- Tombol aksi: lihat detail dan pengaturan -->
                        <td class="px-6">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('unit.permohonan.show', $baris['id']) }}" aria-label="Lihat detail {{ $baris['no_surat'] }}" class="flex h-7 w-7 items-center justify-center rounded-md bg-[#f2f4f6] text-brand-800 transition hover:bg-gray-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </a>
                                <a href="#" aria-label="Pengaturan {{ $baris['no_surat'] }}" class="flex h-7 w-7 items-center justify-center rounded-md bg-[#f2f4f6] text-gray-500 transition hover:bg-gray-200">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <!-- Keadaan kosong: tidak ada data yang cocok dengan filter / pencarian -->
                    <tr>
                        <td colspan="6" class="h-[120px] text-center text-[14px] text-gray-500">Tidak ada permohonan yang cocok dengan filter atau pencarian Anda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer kartu: keterangan jumlah data + tombol paginasi (tautan, bukan tombol statis) -->
    <div class="flex h-16 items-center justify-between border-t border-gray-200 px-6">
        <p class="text-[14px] text-gray-500">Menampilkan {{ $permohonan->count() }} dari {{ $permohonan->total() }} permohonan</p>
        <div class="flex items-center gap-2">
            @if ($permohonan->previousPageUrl())
                <a href="{{ $permohonan->previousPageUrl() }}" class="flex h-[33px] items-center rounded-lg border border-gray-200 bg-white px-4 text-[14px] font-semibold text-gray-900 transition hover:bg-gray-50">Sebelumnya</a>
            @else
                <span class="flex h-[33px] cursor-not-allowed items-center rounded-lg border border-gray-200 bg-white px-4 text-[14px] font-semibold text-gray-500">Sebelumnya</span>
            @endif

            @if ($permohonan->nextPageUrl())
                <a href="{{ $permohonan->nextPageUrl() }}" class="flex h-[33px] items-center rounded-lg bg-brand-800 px-4 text-[14px] font-semibold text-white transition hover:bg-brand-700">Selanjutnya</a>
            @else
                <span class="flex h-[33px] cursor-not-allowed items-center rounded-lg bg-brand-800/50 px-4 text-[14px] font-semibold text-white">Selanjutnya</span>
            @endif
        </div>
    </div>
</section>

@endsection
