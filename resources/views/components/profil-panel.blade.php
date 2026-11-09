{{--
    Isi halaman Profil universal (banner, Informasi Pribadi, Keamanan Akun, Peran & Hak Akses, Preferensi Notifikasi)
    + modal Edit Profil. Pemakaian: <x-profil-panel :user="$user" :update-action="route('...profil.update')" />

    Props:
      user         : objek/model pengguna. Properti yang dibaca: name, status, role, unit, institution, nip, email, phone, phone_masked,
                     access_level, password_changed_at, last_login, active_session, role_description, assignment_status,
                     notify_status_reminder (bool), notify_weekly_summary (bool)
      updateAction : alamat tujuan form Edit Profil (diteruskan ke x-profil-modal)
--}}
@props(['user', 'updateAction' => ''])

@php
    /* Kelas bersama untuk kartu putih agar seragam */
    $kelasKartu = 'rounded-2xl border border-gray-100 bg-white px-6 pb-5 pt-[27px] shadow-[0_1px_3px_rgba(15,23,42,0.06)]';

    /* Baris "Informasi Pribadi": [label, isi]. Dua kolom: kiri-kanan bergantian */
    $infoPribadi = [
        ['Nama Lengkap',    $user->name],      ['NIP',           $user->nip],
        ['Email Kedinasan', $user->email],     ['Nomor Telepon', $user->phone_masked],
        ['Unit Kerja',      $user->unit],      ['Instansi',      $user->institution],
        ['Jabatan',         $user->role],      ['Hak Akses',     $user->access_level],
    ];

    /* Item "Keamanan Akun": [judul, keterangan, warna lingkaran ikon, path ikon, tindakan di kanan (pil | tautan | null)] */
    $keamanan = [
        ['Verifikasi akun', 'Akun terdaftar dan aktif',                          'bg-[#dff3ec] text-[#22a263]', 'M4.5 12.75l6 6 9-13.5', 'pil'],
        ['Kata sandi',      'Terakhir diubah ' . $user->password_changed_at,     'bg-[#e2eafb] text-[#3b6fd8]', 'M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z', 'tautan'],
        ['Login terakhir',  $user->last_login,                                   'bg-[#fcf0cd] text-[#d9a21b]', 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z', null],
        ['Sesi aktif',      $user->active_session,                               'bg-[#dcefe9] text-[#1f8f78]', 'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25', null],
    ];

    /* Baris "Preferensi Notifikasi": [name input, judul, keterangan, aktif?] */
    $preferensi = [
        ['notif_pengingat_status',   'Pengingat tenggat dan perubahan status', 'Notifikasi untuk paket yang memerlukan tindakan', $user->notify_status_reminder],
        ['notif_ringkasan_mingguan', 'Ringkasan aktivitas mingguan',           'Ringkasan dikirim ke email kedinasan',            $user->notify_weekly_summary],
    ];
@endphp

<!-- Pembungkus isi halaman: sedikit inset kiri-kanan agar sejajar dengan mockup -->
<div class="xl:pl-2 xl:pr-[15px]">

<!-- ===================== BANNER PROFIL: latar tosca muda, foto, nama, jabatan, dan tombol Edit Profile ===================== -->
<section class="relative h-[193px] overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-[0_1px_3px_rgba(15,23,42,0.06)]">

    <!-- Pita latar tosca muda di bagian atas banner -->
    <div class="absolute inset-x-0 top-0 h-[72px] bg-[#e9f6f6]"></div>

    <!-- Foto profil bulat bercincin putih (menumpuk di batas pita). TODO Backend: tampilkan foto pengguna bila ada -->
    <div class="absolute left-[31px] top-[51px] flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-[#cce9e4] ring-4 ring-white">
        <svg class="mt-3 h-[60px] w-[60px] text-[#5fb8a8]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 12a4.5 4.5 0 100-9 4.5 4.5 0 000 9zm-8.25 9.75a8.25 8.25 0 0116.5 0v.75H3.75v-.75z"/>
        </svg>
    </div>

    <!-- Nama, status, jabatan, dan unit; tombol Edit Profile di sisi kanan -->
    <div class="absolute inset-x-0 bottom-0 flex h-[121px] items-center justify-between pl-[136px] pr-[43px]">
        <div>
            <div class="flex items-center gap-7">
                <h2 class="text-[22px] font-bold leading-[30px]">{{ $user->name }}</h2>
                <!-- Pil status akun -->
                <span class="inline-flex h-[22px] w-[72px] items-center justify-center rounded-full bg-[#d5f1e3] text-[10px] font-bold text-[#1c8a52]">{{ $user->status }}</span>
            </div>
            <p class="mt-[2px] text-[13px] leading-5 text-gray-700">{{ $user->role }}</p>
            <p class="mt-[2px] text-[13px] leading-5 text-gray-500">{{ $user->unit }} · {{ $user->institution }}</p>
        </div>

        <!-- "Edit Profile" membuka modal Edit Profil (x-profil-modal di bagian bawah komponen ini) -->
        <button type="button" data-buka-modal="modal-edit-profil"
                class="h-[41px] rounded-lg bg-tosca-900 px-[23px] text-[14px] font-semibold text-white transition hover:bg-tosca-800">Edit Profile</button>
    </div>
</section>

<!-- ===================== BARIS 2: INFORMASI PRIBADI (kiri, 60%) + KEAMANAN AKUN (kanan, 40%) ===================== -->
<div class="mt-6 grid grid-cols-1 gap-[25px] xl:grid-cols-[3fr_2fr]">

    <!-- ---------- Kartu Informasi Pribadi ---------- -->
    <section class="{{ $kelasKartu }}">
        <div class="border-b border-gray-200 pb-px">
            <h2 class="text-[13px] font-semibold leading-5">Informasi Pribadi</h2>
            <p class="text-[12px] leading-5 text-gray-500">Informasi dasar yang terhubung dengan akun Anda</p>
        </div>

        <!-- Grid dua kolom label-isi. Kolom kanan dimulai pada 371px dari tepi kiri kartu -->
        <dl class="mt-[23px] grid grid-cols-1 gap-y-4 md:grid-cols-[371px_1fr]">
            @foreach ($infoPribadi as [$label, $isi])
                <div>
                    <dt class="text-[12px] uppercase leading-4 text-gray-500">{{ $label }}</dt>
                    <dd class="mt-[3px] text-[13px] leading-5 text-gray-900">{{ $isi }}</dd>
                </div>
            @endforeach
        </dl>

        <!-- Catatan kecil tentang verifikasi perubahan data kepegawaian -->
        <p class="mt-5 rounded-md bg-[#f3f6f8] px-3 py-2 text-[12px] leading-4 text-gray-600">Perubahan data kepegawaian mungkin memerlukan verifikasi administrator.</p>
    </section>

    <!-- ---------- Kartu Keamanan Akun ---------- -->
    <section class="{{ $kelasKartu }}">
        <div class="border-b border-gray-200 pb-px">
            <h2 class="text-[13px] font-semibold leading-5">Keamanan Akun</h2>
            <p class="text-[12px] leading-5 text-gray-500">Pengaturan akses dan sesi masuk</p>
        </div>

        <ul>
            <!-- MULAI LOOP: Item keamanan akun (verifikasi, kata sandi, login terakhir, sesi aktif) -->
            @foreach ($keamanan as [$judul, $keterangan, $kelasIkon, $pathIkon, $aksi])
                <li class="flex items-center gap-3 pb-[14px] {{ $loop->first ? 'pt-2' : 'border-t border-gray-200 pt-[14px]' }}">
                    <span class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full {{ $kelasIkon }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $pathIkon }}" />
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-[12.5px] font-medium leading-4">{{ $judul }}</p>
                        <p class="mt-[3px] text-[12px] leading-4 text-gray-500">{{ $keterangan }}</p>
                    </div>

                    <!-- Tindakan di kanan: pil "Terverifikasi" atau tautan "Ubah" (menuju halaman ubah kata sandi) -->
                    @if ($aksi === 'pil')
                        <span class="inline-flex h-[22px] items-center rounded-full bg-[#d9f2e5] px-3 text-[10px] font-bold text-[#1c8a52]">Terverifikasi</span>
                    @elseif ($aksi === 'tautan')
                        <!-- TODO Backend: arahkan ke halaman/modal ubah kata sandi -->
                        <a href="#" class="pr-2 text-[11px] font-bold text-tosca-400 hover:underline">Ubah</a>
                    @endif
                </li>
            @endforeach
            <!-- AKHIR LOOP: Item keamanan akun -->
        </ul>

        <p class="mt-[10px] rounded-md bg-[#f3f6f8] px-3 py-[3px] text-[12px] leading-4 text-gray-600">Jangan bagikan kata sandi atau kode verifikasi.</p>
    </section>
</div>

<!-- ===================== BARIS 3: PERAN & HAK AKSES (kiri) + PREFERENSI NOTIFIKASI (kanan) ===================== -->
<div class="mt-6 grid grid-cols-1 gap-[25px] xl:grid-cols-[540fr_549fr]">

    <!-- ---------- Kartu Peran & Hak Akses ---------- -->
    <section class="{{ $kelasKartu }}">
        <h2 class="text-[13px] font-semibold leading-5">Peran &amp; Hak Akses</h2>
        <p class="text-[12px] leading-5 text-gray-500">Akses menu berdasarkan penugasan akun</p>

        <!-- Kotak peran aktif: ikon centang hijau + nama peran + deskripsi hak -->
        <div class="mt-[7px] flex items-center gap-3 rounded-lg border border-[#cdeae4] bg-[#edf9f7] px-[19px] py-[5px]">
            <span class="flex h-[18px] w-[18px] shrink-0 items-center justify-center rounded-full bg-[#22b35e]">
                <svg class="h-[10px] w-[10px]" fill="none" stroke="white" stroke-width="3.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12.5l4.5 4.5L19 7.5" /></svg>
            </span>
            <div>
                <p class="text-[12.5px] font-medium leading-4">{{ $user->role }}</p>
                <p class="mt-[2px] text-[12px] leading-4 text-gray-500">{{ $user->role_description }}</p>
            </div>
        </div>

        <p class="mt-6 text-[12px] uppercase leading-4 text-gray-500">Status Penugasan</p>
        <p class="mt-[3px] text-[13px] leading-5 text-gray-900">{{ $user->assignment_status }}</p>
    </section>

    <!-- ---------- Kartu Preferensi Notifikasi ---------- -->
    <section class="{{ $kelasKartu }}">
        <h2 class="text-[13px] font-semibold leading-5">Preferensi Notifikasi</h2>
        <p class="text-[12px] leading-5 text-gray-500">Pilih pemberitahuan yang ingin diterima</p>

        {{--
            TODO Backend: isi action dengan route penyimpanan preferensi. Setiap saklar adalah checkbox dengan name unik;
            bila ingin tersimpan otomatis saat digeser, kirim form lewat JavaScript pada event change.
        --}}
        <form method="POST" action="" class="mt-[7px]">
            @csrf
            <!-- MULAI LOOP: Baris preferensi notifikasi (satu iterasi = satu saklar) -->
            @foreach ($preferensi as [$nama, $judul, $keterangan, $aktif])
                <div class="flex items-start justify-between gap-4 py-[10px] {{ $loop->first ? '' : 'border-t border-gray-200' }}">
                    <div>
                        <p class="text-[13px] font-medium leading-5">{{ $judul }}</p>
                        <p class="text-[12px] leading-4 text-gray-500">{{ $keterangan }}</p>
                    </div>

                    <!-- Saklar on/off: checkbox disembunyikan, tampilan digambar oleh dua elemen span (latar + bulatan) -->
                    <label class="relative inline-flex h-6 w-10 shrink-0 cursor-pointer">
                        <input type="checkbox" name="{{ $nama }}" value="1" class="peer sr-only" @checked($aktif)>
                        <span class="absolute inset-0 rounded-full bg-gray-300 transition peer-checked:bg-tosca-900 peer-focus-visible:ring-2 peer-focus-visible:ring-tosca-400"></span>
                        <span class="absolute left-[3px] top-[3px] h-[18px] w-[18px] rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
                    </label>
                </div>
            @endforeach
            <!-- AKHIR LOOP: Baris preferensi notifikasi -->
        </form>
    </section>
</div>

</div>

<!-- Modal Edit Profil (disembunyikan secara default, dibuka tombol "Edit Profile" di banner) -->
<x-profil-modal :user="$user" :action="$updateAction" />
