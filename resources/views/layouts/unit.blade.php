@extends('layouts.base')

{{-- Layout role User Unit: sidebar navigasi + header profil. Role lain membuat layout sendiri (mis. layouts/wadir.blade.php) --}}
@section('badan')

@php
    /*
     * Daftar menu sidebar role User Unit.
     * 'kunci' dicocokkan dengan @section('menu_aktif') pada halaman anak untuk menandai menu yang sedang dibuka.
     * 'rute'  diisi nama route; jika route belum dibuat, tautan sementara diarahkan ke "#".
     */
    $menuSidebar = [
        ['kunci' => 'dashboard',  'label' => 'Dashboard',  'rute' => 'unit.dashboard',
         'ikon'  => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z'],
        ['kunci' => 'permohonan', 'label' => 'Permohonan', 'rute' => 'unit.permohonan.index',
         'ikon'  => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
        ['kunci' => 'tracking',   'label' => 'Tracking',   'rute' => 'unit.tracking.index',
         'ikon'  => 'M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z'],
        ['kunci' => 'dokumen',    'label' => 'Dokumen',    'rute' => 'unit.dokumen.index',
         'ikon'  => 'M16.5 8.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v8.25A2.25 2.25 0 006 16.5h2.25m8.25-8.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-7.5A2.25 2.25 0 018.25 18v-1.5m8.25-8.25h-6a2.25 2.25 0 00-2.25 2.25v6'],
    ];
    $menuAktif = trim($__env->yieldContent('menu_aktif', 'dashboard'));
@endphp

<div class="flex min-h-screen">

    <!-- ===================== SIDEBAR NAVIGASI UTAMA (lebar tetap 260px) ===================== -->
    <aside class="sticky top-0 flex h-screen w-[260px] shrink-0 flex-col bg-brand-800 text-white">

        <!-- Bagian logo: kotak ikon hijau-teal + nama aplikasi -->
        <div class="flex items-center gap-3 px-4 pb-0 pt-6">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21" />
                </svg>
            </div>
            <div>
                <p class="text-[16px] font-bold leading-tight">SIMPATIK-PBJ</p>
                <p class="text-[12px] leading-tight text-white/70">User Unit Platform</p>
            </div>
        </div>

        <!-- Daftar menu: menu aktif diberi latar teal yang lebih terang -->
        <nav class="mt-8 space-y-1.5 px-4">
            @foreach ($menuSidebar as $menu)
                @php $aktif = $menu['kunci'] === $menuAktif; @endphp
                <a href="{{ Route::has($menu['rute']) ? route($menu['rute']) : '#' }}"
                   class="flex h-11 items-center gap-3 rounded-lg px-4 text-[15px] font-medium transition
                          {{ $aktif ? 'bg-[#135d6d] text-white' : 'text-white/75 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu['ikon'] }}" />
                    </svg>
                    {{ $menu['label'] }}
                </a>
            @endforeach
        </nav>

        <!-- Info unit pengguna di bawah menu, dipisahkan garis tipis -->
        <div class="mx-4 mt-8 border-t border-white/15 pt-5">
            <p class="text-[13px] font-semibold leading-tight">Unit Perencanaan &amp; IT</p>
            <p class="mt-0.5 text-[12px] text-white/60">Fakultas Teknik - Kampus Utama</p>
        </div>
    </aside>

    <!-- ===================== AREA KONTEN UTAMA ===================== -->
    <div class="flex min-w-0 flex-1 flex-col">

        <!-- Header atas: judul halaman di kiri, notifikasi dan profil pengguna di kanan -->
        <header class="flex h-16 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-6">
            <h1 class="text-[20px] font-bold">@yield('judul_halaman', 'Dashboard')</h1>

            <div class="flex items-center gap-6">
                <!-- Tombol notifikasi dengan lencana merah jumlah notifikasi -->
                <button type="button" class="relative text-gray-500 transition hover:text-gray-700" aria-label="Notifikasi">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span class="absolute -right-1 -top-1.5 flex h-[15px] w-[15px] items-center justify-center rounded-full bg-[#eb5656] text-[10px] font-semibold leading-none text-white">3</span>
                </button>

                <!-- Profil pengguna: nama, jabatan, dan avatar -->
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-[14px] font-semibold leading-tight">Hendra Wijaya, M.T.</p>
                        <p class="text-[12px] leading-tight text-gray-500">PPK - Unit FT</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-800 text-[14px] font-semibold text-white">HW</div>
                </div>
            </div>
        </header>

        <!-- Isi halaman: diisi oleh halaman anak melalui section bernama "konten" -->
        <main class="flex-1 p-6">
            @yield('konten')
        </main>
    </div>
</div>

@endsection
