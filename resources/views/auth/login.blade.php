<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - SIMPATIK-PBJ</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-slate-800 antialiased">

@php
    /*
     * Data langkah timeline "Status Pengadaan Terkini".
     * state: 'completed' = selesai (titik hijau + ikon centang),
     *        'active'    = sedang berjalan (titik oranye + kartu disorot),
     *        'pending'   = belum dimulai (titik abu-abu)
     */
    $langkahTimeline = [
        ['judul' => 'Pengajuan Usulan Barang/Jasa',         'waktu' => '14 Okt 2024 - 09:30 WIB', 'state' => 'completed'],
        ['judul' => 'Review & Verifikasi Anggaran',         'waktu' => '15 Okt 2024 - 14:15 WIB', 'state' => 'completed'],
        ['judul' => 'Persetujuan Pejabat Pembuat Komitmen', 'waktu' => '16 Okt 2024 - 10:00 WIB', 'state' => 'active'],
        ['judul' => 'Proses Pemilihan Penyedia',            'waktu' => 'Estimasi: 18 Okt 2024',   'state' => 'pending'],
    ];
@endphp

<!-- Wrapper utama: layout terbagi dua (split-screen). Di layar kecil, panel kiri disembunyikan -->
<div class="flex min-h-screen">

    <!-- ===================== PANEL KIRI (45%): branding + widget tracking ===================== -->
    <aside class="relative hidden w-[45%] flex-col justify-between overflow-hidden bg-brand-800 px-12 py-12 text-white lg:flex"
           style="background-image: radial-gradient(ellipse 70% 45% at 20% 0%, #136170 0%, rgba(15,75,91,0) 100%);">

        <!-- Bagian atas: badge nama aplikasi, judul, dan deskripsi -->
        <div>
            <!-- Badge "SIMPATIK-PBJ" dengan ikon kubus -->
            <div class="inline-flex items-center gap-2 rounded-lg border border-white/15 bg-white/10             px-3.5 py-2 text-[16px] font-bold">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
                SIMPATIK-PBJ
            </div>

            <!-- Judul utama -->
            <h1 class="mt-7 text-[32px] font-bold leading-[1.25]">
                Sistem Informasi Manajemen &amp; Pengawasan Tracking Pengadaan Barang &amp; Jasa
            </h1>

            <!-- Deskripsi singkat -->
            <p class="mt-4 max-w-[520px] text-[14px] leading-[1.65] text-white/70">
                Solusi digital terintegrasi untuk efisiensi, akuntabilitas, dan transparansi proses pengadaan di lingkungan universitas.
            </p>
        </div>

        <!-- Widget kaca: "Status Pengadaan Terkini" -->
        <section class="rounded-[20px] border border-white/10 bg-white/[0.06] p-6">

            <!-- Header widget: titik hijau + judul + kode permohonan, dipisahkan garis tipis di bawahnya -->
            <div class="mb-4 flex items-center justify-between border-b border-white/10 pb-4">
                <div class="flex items-center gap-2.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-accent"></span>
                    <h2 class="text-[15px] font-semibold">Status Pengadaan Terkini</h2>
                </div>
                <span class="rounded-md bg-[#24646e] px-2 py-1 text-[11px] font-semibold tracking-wide text-[#d1f2ed]">PR-2024-089</span>
            </div>

            <!-- Daftar langkah: tiap langkah berupa kartu terpisah, dirender lewat perulangan -->
            <ol class="space-y-2">
                @foreach ($langkahTimeline as $langkah)
                    @php
                        $selesai = $langkah['state'] === 'completed';
                        $aktif   = $langkah['state'] === 'active';
                    @endphp

                    <!-- Kartu langkah; langkah aktif memakai latar lebih terang dan border tipis -->
                    <li class="flex items-center gap-3 rounded-[10px] border px-3.5 py-3
                        {{ $aktif ? 'border-white/25 bg-white/[0.18]' : 'border-transparent bg-white/10' }}">

                        <!-- Titik penanda: hijau (selesai), oranye (aktif), abu-abu (menunggu) -->
                        <span class="h-2 w-2 shrink-0 rounded-full
                            {{ $selesai ? 'bg-accent' : ($aktif ? 'bg-amber-500' : 'bg-[#58828d]') }}"></span>

                        <!-- Judul langkah dan keterangan waktu -->
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-semibold leading-snug">{{ $langkah['judul'] }}</p>
                            <p class="text-[12px] leading-snug text-white/65">{{ $langkah['waktu'] }}</p>
                        </div>

                        <!-- Ikon di sisi kanan: centang untuk langkah selesai, jam untuk lainnya -->
                        @if ($selesai)
                            <svg class="h-[18px] w-[18px] shrink-0 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg class="h-[18px] w-[18px] shrink-0 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </li>
                @endforeach
            </ol>
        </section>

        <!-- Footer panel kiri: hak cipta dan versi aplikasi -->
        <footer class="flex items-center justify-between text-[13px] text-white/50">
            <span>&copy; 2024 Universitas Procurement System</span>
            <span>v2.4.0-Beta</span>
        </footer>
    </aside>

    <!-- ===================== PANEL KANAN (55%): form login ===================== -->
    <main class="flex flex-1 items-center justify-center bg-white px-6 py-12">

        <!-- Kontainer form dengan lebar maksimum sekitar 440px -->
        <div class="w-full max-w-[440px]">

            <!-- Judul sambutan -->
            <div class="mb-8">
                <h2 class="text-[28px] font-extrabold leading-tight text-slate-900">Selamat Datang</h2>
                <p class="mt-1 text-[15px] text-slate-500">Masuk ke akun Anda untuk mengelola pengadaan</p>
            </div>

            <!-- Form login. Belum ada route POST, jadi pengiriman dicegah sementara (lihat skrip di bawah) -->
            <form id="form-login" method="POST" action="#" class="space-y-5">
                @csrf

                <!-- Grup input: Alamat Email -->
                <div>
                    <label for="email" class="mb-2 block text-[13px] font-semibold text-slate-900">Alamat Email</label>
                    <div class="relative">
                        <!-- Ikon amplop di sisi kiri input -->
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="operator@universitas.ac.id" autocomplete="email" required
                               class="h-11 w-full rounded-lg border border-slate-200 bg-surface pl-10 pr-4 text-[14px] text-slate-900 placeholder-slate-500 outline-none transition focus:border-accent focus:bg-white focus:ring-2 focus:ring-accent/20">
                    </div>
                </div>

                <!-- Grup input: Kata Sandi (dengan tombol tampil/sembunyi) -->
                <div>
                    <label for="password" class="mb-2 block text-[13px] font-semibold text-slate-900">Kata Sandi</label>
                    <div class="relative">
                        <!-- Ikon gembok di sisi kiri input -->
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        <input id="password" name="password" type="password" placeholder="••••••••" autocomplete="current-password" required
                               class="h-11 w-full rounded-lg border border-slate-200 bg-surface pl-10 pr-11 text-[14px] text-slate-900 placeholder-slate-500 outline-none transition focus:border-accent focus:bg-white focus:ring-2 focus:ring-accent/20">
                        <!-- Tombol mata untuk menampilkan / menyembunyikan kata sandi -->
                        <button type="button" id="toggle-password" aria-label="Tampilkan kata sandi"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-900 transition hover:text-slate-600">
                            <!-- Ikon mata tercoret (kata sandi tersembunyi) -->
                            <svg id="ikon-mata-tutup" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                            <!-- Ikon mata terbuka (kata sandi terlihat) -->
                            <svg id="ikon-mata-buka" class="hidden h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Baris opsi: checkbox "Ingat Saya" dan tautan "Lupa Password?" -->
                <div class="flex items-center justify-between">
                    <label class="flex cursor-pointer items-center gap-2 text-[14px] text-slate-500">
                        <input type="checkbox" name="remember" checked class="h-[18px] w-[18px] rounded border-slate-300 accent-brand-800">
                        Ingat Saya
                    </label>
                    <a href="#" class="text-[13px] font-bold text-accent transition hover:text-brand-700">Lupa Password?</a>
                </div>

                <!-- Tombol utama: Masuk ke Dashboard (teal gelap) -->
                <button type="submit"
                        class="!mt-7 h-[46px] w-full rounded-lg bg-brand-800 text-[15px] font-semibold text-white shadow-md shadow-brand-800/20 transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-800 focus:ring-offset-2">
                    Masuk ke Dashboard
                </button>
            </form>

            <!-- Tombol sekunder: Masuk dengan SSO Universitas (outline putih) -->
            <button type="button"
                    class="mt-4 flex h-[45px] w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white text-[14px] font-semibold text-slate-900 shadow-sm transition hover:bg-slate-50">
                <!-- Ikon perisai -->
                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                Masuk dengan SSO Universitas
            </button>

            <!-- Kotak bantuan di bagian bawah form -->
            <div class="mt-8 flex items-center gap-2.5 rounded-lg bg-slate-100/80 px-3 py-2.5 text-[13px] text-slate-500">
                <!-- Ikon tanda tanya -->
                <svg class="h-[18px] w-[18px] shrink-0 text-slate-900" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
                <p>Butuh bantuan akses? Hubungi <a href="#" class="font-bold text-brand-800 hover:underline">Layanan IT Helpdesk</a></p>
            </div>
        </div>
    </main>
</div>

<!-- Skrip kecil: tampil/sembunyi kata sandi & pencegah submit sementara -->
<script>
    const inputSandi = document.getElementById('password');
    const tombolMata = document.getElementById('toggle-password');
    const ikonTutup  = document.getElementById('ikon-mata-tutup');
    const ikonBuka   = document.getElementById('ikon-mata-buka');

    // Ganti tipe input antara "password" dan "text", lalu tukar ikon mata
    tombolMata.addEventListener('click', () => {
        const tersembunyi = inputSandi.type === 'password';
        inputSandi.type = tersembunyi ? 'text' : 'password';
        ikonTutup.classList.toggle('hidden', tersembunyi);
        ikonBuka.classList.toggle('hidden', !tersembunyi);
        tombolMata.setAttribute('aria-label', tersembunyi ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });

    // Sementara (belum ada autentikasi): tombol "Masuk ke Dashboard" langsung membuka halaman dashboard
    document.getElementById('form-login').addEventListener('submit', (e) => {
        e.preventDefault();
        window.location.href = "{{ route('unit.dashboard') }}";
    });
</script>
</body>
</html>
