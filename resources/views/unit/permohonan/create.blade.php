@extends('layouts.unit')

@section('judul_halaman', 'Buat Permohonan')
@section('menu_aktif', 'permohonan')

@section('konten')

@php
    /* Kelas Tailwind bersama untuk semua kolom isian agar tampilannya seragam */
    $kelasInput = 'h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-[14px] text-gray-900 placeholder-gray-500 outline-none transition focus:border-accent focus:ring-2 focus:ring-accent/20';
    $kelasLabel = 'mb-1.5 block text-[14px] font-semibold text-gray-900';
@endphp

<!-- ===================== BANNER PERINGATAN ===================== -->
<!-- Pemberitahuan kuning: sistem hanya mencatat data, tidak membuat surat resmi -->
<div class="flex items-center gap-3 rounded-lg border border-[#f2c84b] bg-[#fdf9e6] px-4 py-3.5">
    <svg class="h-[18px] w-[18px] shrink-0 text-[#f2c84b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
    </svg>
    <p class="text-[14px] font-medium">Penting: Sistem tidak membuat surat resmi otomatis. Aplikasi ini hanya mencatat data permohonan untuk dilacak di unit PBJ Universitas.</p>
</div>

<!-- ===================== KARTU FORMULIR ===================== -->
<section class="mt-6 rounded-xl border border-gray-200 bg-white p-8">
    <h2 class="mb-5 text-[18px] font-bold">Formulir Usulan Pengadaan Unit</h2>

    <!-- Form utama; belum ada route penyimpanan sehingga pengiriman dicegah oleh skrip di bagian bawah -->
    <form id="form-permohonan" method="POST" action="#" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <!-- Baris 1: dua kolom sejajar (Nomor Surat dan Judul Pengadaan) -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <!-- Grup isian: Nomor Surat Resmi Unit -->
            <div>
                <label for="nomor_surat" class="{{ $kelasLabel }}">Nomor Surat Resmi Unit *</label>
                <input id="nomor_surat" name="nomor_surat" type="text" required placeholder="Contoh: 021/UN1.FT/PBJ/2026" class="{{ $kelasInput }}">
            </div>

            <!-- Grup isian: Judul Pengadaan / Perihal -->
            <div>
                <label for="judul" class="{{ $kelasLabel }}">Judul Pengadaan / Perihal *</label>
                <input id="judul" name="judul" type="text" required placeholder="Masukkan perihal pengadaan barang / jasa" class="{{ $kelasInput }}">
            </div>
        </div>

        <!-- Baris 2: pilihan unit pengusul (lebar penuh) -->
        <div>
            <label for="unit" class="{{ $kelasLabel }}">Asal Unit Pengusul *</label>
            <div class="relative">
                <select id="unit" name="unit" required class="{{ $kelasInput }} appearance-none pr-10">
                    <option value="FT-02" selected>Fakultas Teknik - Perencanaan &amp; IT (FT-02)</option>
                </select>
                <!-- Ikon panah bawah untuk dropdown -->
                <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-900" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
        </div>

        <!-- Baris 3: tujuan dan alasan kebutuhan (area teks) -->
        <div>
            <label for="tujuan" class="{{ $kelasLabel }}">Tujuan &amp; Alasan Kebutuhan Pengadaan *</label>
            <textarea id="tujuan" name="tujuan" rows="4" required placeholder="Jelaskan urgensi, deskripsi singkat, dan perkiraan manfaat dari pengadaan ini..."
                      class="{{ $kelasInput }} h-[99px] resize-y py-2.5"></textarea>
        </div>

        <!-- Baris 4: area unggah lampiran dengan fitur drag-and-drop -->
        <div>
            <label for="lampiran" class="{{ $kelasLabel }}">Upload Lampiran Pendukung (RAB, KAK, Spesifikasi) *</label>

            <!-- Zona drag-and-drop: seluruh area bisa diklik; border putus-putus teal berubah saat file diseret ke atasnya -->
            <label id="zona-unggah" for="lampiran"
                   class="flex h-[143px] cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-accent bg-surface px-4 text-center transition">
                <!-- Ikon awan dengan panah unggah -->
                <svg class="h-[30px] w-[30px] text-accent" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                </svg>
                <span class="mt-3.5 text-[14px] font-semibold">Tarik dan lepaskan file di sini, atau klik tombol jelajahi</span>
                <span class="mt-1 text-[12px] text-gray-500">Hanya file PDF, XLSX, atau ZIP (Maksimal 10MB per file)</span>
                <!-- Daftar nama file yang sudah dipilih (diisi oleh skrip) -->
                <span id="daftar-berkas" class="mt-1 text-[12px] font-medium text-accent"></span>

                <input id="lampiran" name="lampiran[]" type="file" multiple accept=".pdf,.xlsx,.zip" class="sr-only">
            </label>
        </div>

        <!-- Baris tombol aksi di kanan bawah: Simpan Draft (outline) dan Kirim Permohonan (teal) -->
        <div class="flex justify-end gap-3 !pt-4">
            <button type="button" class="h-10 rounded-lg border border-gray-200 bg-white px-6 text-[14px] font-semibold text-gray-500 transition hover:bg-gray-50">
                Simpan Draft
            </button>
            <button type="submit" class="h-10 rounded-lg bg-brand-800 px-6 text-[14px] font-semibold text-white transition hover:bg-brand-700">
                Kirim Permohonan
            </button>
        </div>
    </form>
</section>

@endsection

@push('skrip')
<script>
    const zona   = document.getElementById('zona-unggah');
    const input  = document.getElementById('lampiran');
    const daftar = document.getElementById('daftar-berkas');

    // Tampilkan nama file yang dipilih di dalam zona unggah
    const tampilkanBerkas = () => {
        daftar.textContent = Array.from(input.files).map(f => f.name).join(', ');
    };
    input.addEventListener('change', tampilkanBerkas);

    // Beri sorotan saat file diseret ke atas zona
    ['dragenter', 'dragover'].forEach(nama => zona.addEventListener(nama, e => {
        e.preventDefault();
        zona.classList.add('bg-accent/5', 'border-solid');
    }));

    ['dragleave', 'drop'].forEach(nama => zona.addEventListener(nama, e => {
        e.preventDefault();
        zona.classList.remove('bg-accent/5', 'border-solid');
    }));

    // Saat file dilepas, masukkan ke input file agar ikut terkirim bersama form
    zona.addEventListener('drop', e => {
        input.files = e.dataTransfer.files;
        tampilkanBerkas();
    });

    // Belum ada route penyimpanan: cegah pengiriman form sementara
    document.getElementById('form-permohonan').addEventListener('submit', e => e.preventDefault());
</script>
@endpush
