{{--
    Panel Notifikasi universal - dipakai semua role (User Unit, Wadir 2, dst.).
    Pemakaian: <x-notifikasi-panel :notifications="$notifikasi" :tab-counts="$jumlahTab" :active-tab="$tabAktif" route-prefix="unit.notifikasi" />

    Props:
      notifications : koleksi objek notifikasi. Properti yang dibaca per item:
                        id, icon (approval | kontrak | reminder | info), title, time, message, category,
                        is_read (bool), status_label, letter_number, detail_url
      tabCounts     : jumlah per tab, kunci: semua, belum-dibaca, approval, reminder
      activeTab     : kunci tab yang sedang dibuka
      routePrefix   : awalan nama route role. Komponen memakai route "{prefix}.index", "{prefix}.dibaca-semua", dan "{prefix}.dibaca"

    Halaman pemakai harus mengisi section header_halaman dengan komentar singkat agar header standar layout tidak ikut tampil
    (header Notifikasi digambar di dalam komponen ini).
--}}
@props(['notifications', 'tabCounts', 'activeTab' => 'semua', 'routePrefix'])

@php
    /* Tab filter: kunci => [label, gaya lencana jumlah saat tab tidak aktif] */
    $tabs = [
        'semua'        => ['Semua',        'bg-[#e5e7eb] text-gray-600'],
        'belum-dibaca' => ['Belum Dibaca', 'bg-[#e5e7eb] text-gray-600'],
        'approval'     => ['Approval',     'bg-[#d4efe9] text-tosca-400'],
        'reminder'     => ['Reminder',     'bg-[#d4efe9] text-tosca-400'],
    ];

    /* Gaya ikon notifikasi: kunci ikon => [warna lingkaran + ikon, path ikon utama, path tambahan opsional] */
    $dokumen = 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z';
    $gayaIkon = [
        'approval' => ['bg-[#d8f5e5] text-[#22b35e]', $dokumen, 'M9.5 15l1.8 1.8 3.4-3.6'],
        'kontrak'  => ['bg-[#dbe8fb] text-[#3b82f6]', $dokumen, 'M8.25 15.75h7.5m-7.5 3H12'],
        'reminder' => ['bg-[#fdf0c8] text-[#e0a41a]', 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z', null],
        'info'     => ['bg-[#e9ecef] text-gray-500',  'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z', null],
    ];
@endphp

<div class="-mt-[17px] xl:px-[15px]">

    <!-- ===================== HEADER: judul + subjudul di kiri, tombol "Tandai Semua Dibaca" di kanan ===================== -->
    <header class="relative mt-[6px] flex items-start justify-between gap-4">
        <div>
            <h1 class="text-[24px] font-bold leading-8">Notifikasi</h1>
            <p class="mt-[3px] text-[14px] leading-5 text-gray-700">Pantau seluruh aktivitas persetujuan, kontrak, dan pembayaran pengadaan Anda.</p>
        </div>

        <!-- Form POST + token CSRF. TODO Backend: ubah semua notifikasi milik pengguna menjadi sudah dibaca (isi kolom read_at) -->
        <form method="POST" action="{{ route($routePrefix . '.dibaca-semua') }}" class="mt-[8px] shrink-0">
            @csrf
            <button type="submit" class="flex h-[37px] items-center gap-2 rounded-lg border border-gray-200 bg-white px-[15px] text-[14px] font-medium text-gray-900 shadow-[0_1px_2px_rgba(15,23,42,0.04)] transition hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M1.5 12.75l6 6 9-13.5M10.5 17.25l1.5 1.5 9-13.5" />
                </svg>
                Tandai Semua Dibaca
            </button>
        </form>
    </header>

    <!-- ===================== TAB FILTER (Semua, Belum Dibaca, Approval, Reminder) ===================== -->
    <nav class="mt-[26px] flex flex-wrap gap-[9px]" aria-label="Filter notifikasi">
        @foreach ($tabs as $kunci => [$label, $gayaLencana])
            @php $aktif = $kunci === $activeTab; @endphp
            <a href="{{ route($routePrefix . '.index', $kunci === 'semua' ? [] : ['tab' => $kunci]) }}"
               class="flex h-[33px] items-center gap-2 rounded-lg border px-[14px] text-[14px] transition
                      {{ $aktif ? 'border-tosca-400 bg-tosca-400 font-semibold text-white' : 'border-gray-200 bg-white font-medium text-gray-800 hover:bg-gray-50' }}">
                {{ $label }}
                <span class="rounded-full px-[7px] py-px text-[11px] font-semibold leading-4 {{ $aktif ? 'bg-white/25 text-white' : $gayaLencana }}">{{ $tabCounts[$kunci] }}</span>
            </a>
        @endforeach
    </nav>

    <!-- ===================== DAFTAR NOTIFIKASI ===================== -->
    <div class="mt-[25px] space-y-[13px]">

        <!-- MULAI LOOP: Kartu notifikasi dari database (satu iterasi = satu $notification). Kartu belum dibaca berlatar biru muda + titik biru -->
        @foreach ($notifications as $notification)
            @php [$kelasIkon, $pathIkon, $pathTambahan] = $gayaIkon[$notification->icon] ?? $gayaIkon['info']; @endphp

            <article class="relative rounded-xl border pb-[15px] pl-[47px] pr-[19px] pt-[17px]
                            {{ $notification->is_read ? 'border-gray-100 bg-white' : 'border-[#d6e2f3] bg-[#e6eefa]' }}">

                <!-- Titik biru penanda belum dibaca (di sisi kiri, rata tengah vertikal) -->
                @unless ($notification->is_read)
                    <span class="absolute left-[20px] top-1/2 h-2 w-2 -translate-y-1/2 rounded-full bg-[#2f6fe4]" aria-label="Belum dibaca"></span>
                @endunless

                <div class="flex gap-4">
                    <!-- Ikon kategori dalam lingkaran berwarna -->
                    <span class="mt-[3px] flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $kelasIkon }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $pathIkon }}" />
                            @if ($pathTambahan)
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $pathTambahan }}" />
                            @endif
                        </svg>
                    </span>

                    <div class="min-w-0 flex-1">
                        <!-- Baris judul + waktu di kanan -->
                        <div class="flex items-start justify-between gap-4">
                            <h2 class="text-[15px] font-bold leading-5">{{ $notification->title }}</h2>
                            <time class="shrink-0 text-[12px] leading-5 text-gray-400">{{ $notification->time }}</time>
                        </div>

                        <!-- Isi pesan dan informasi tambahan -->
                        <p class="mt-[5px] text-[14px] leading-5 text-gray-800">{{ $notification->message }}</p>
                        <p class="mt-[7px] text-[12px] leading-[17px] text-gray-600">Kategori: {{ $notification->category }} | Status: {{ $notification->status_label }}</p>
                        <p class="text-[12px] leading-[17px] text-gray-600">Nomor Surat Permohonan: {{ $notification->letter_number }}</p>

                        <!-- Tombol aksi: Lihat Detail (tautan) dan Tandai Dibaca (form POST, nonaktif jika sudah dibaca) -->
                        <div class="mt-[13px] flex items-center gap-[25px]">
                            <a href="{{ $notification->detail_url }}"
                               class="flex h-7 items-center rounded-md border border-gray-200 bg-white px-[13px] text-[12px] font-semibold text-gray-900 transition hover:bg-gray-50">Lihat Detail</a>

                            <form method="POST" action="{{ route($routePrefix . '.dibaca', $notification->id) }}">
                                @csrf
                                <button type="submit" @disabled($notification->is_read)
                                        class="text-[12px] font-medium {{ $notification->is_read ? 'cursor-not-allowed text-gray-400' : 'text-tosca-400 hover:underline' }}">Tandai Dibaca</button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
        <!-- AKHIR LOOP: Kartu notifikasi -->

        <!-- Keadaan kosong: tampil jika tab yang dipilih tidak punya notifikasi -->
        @if (count($notifications) === 0)
            <div class="rounded-xl border border-gray-100 bg-white py-10 text-center text-[14px] text-gray-500">Tidak ada notifikasi.</div>
        @endif
    </div>
</div>
