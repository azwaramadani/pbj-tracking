@extends('layouts.'.($layoutRole ?? 'keuangan'))

@php $selesai = $pembayaran['status'] === 'selesai'; @endphp

@section('judul_halaman', $selesai ? 'Dokumentasi Penerimaan' : 'Detail Pembayaran')
@section('menu_aktif', $menuAktif ?? 'dashboard')
@section('url_kembali', $urlKembali ?? route('keuangan.dashboard'))
@section('breadcrumb_ekstra', 'Pembayaran Selesai')

@section('konten')

{{--
    Detail Pembayaran Keuangan - satu view untuk dua tampilan, ditentukan oleh $pembayaran['status']:
      'diproses' : "Detail Pembayaran" - Informasi Pembayaran, form Realisasi Pembayaran, Dokumen Pendukung Tambahan (PPBJ),
                   dan zona unggah Konfirmasi Realisasi Bayar (tombol "Kirim Bukti Penerimaan")
      'selesai'  : "Dokumentasi Penerimaan" - banner sukses, Ringkasan Pengadaan, Bukti Realisasi Pembayaran,
                   dan Dokumen & Persetujuan Terkait (semuanya hanya-baca)
    Variabel dari Keuangan\PembayaranController@show:
      $pembayaran : lihat App\Support\Keuangan\DataPenerimaan::cari()
--}}

@php
    // Ikon berkas yang dipakai berulang pada daftar dokumen
    $ikonBerkas = 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z';
@endphp

@if (! $selesai)
    {{-- ===================== KEADAAN A (status "diproses"): DETAIL PEMBAYARAN ===================== --}}

    {{--
        TODO Backend: isi action dengan route "Kirim Bukti Penerimaan". Satu form multipart membungkus kedua kolom, sehingga
        field Realisasi Pembayaran dan berkas bukti transfer terkirim bersamaan.
        Field: pembayaran_id, tanggal_pembayaran, jumlah_dibayar, catatan, bukti_transfer (file pdf/jpg maks 5 MB), pernyataan_dana (checkbox).
        Validasi ulang tipe dan ukuran berkas di server; tolak jika pernyataan_dana tidak dicentang.
    --}}
    <form id="form-realisasi" method="POST" action="" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="pembayaran_id" value="{{ $pembayaran['id'] }}">

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">

            <!-- ---------- KOLOM KIRI: Informasi Pembayaran + Realisasi Pembayaran ---------- -->
            <div class="space-y-6">

                <!-- Kartu Informasi Pembayaran: kotak nilai pencairan, lalu baris label-isi -->
                <section class="rounded-2xl border border-gray-100 bg-white px-5 pb-4 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                    <h2 class="text-[16px] font-bold leading-6">Informasi Pembayaran</h2>

                    <!-- Kotak abu-abu berisi nilai pencairan (angka besar) -->
                    <div class="mt-4 rounded-lg bg-[#f1f2f4] px-4 pb-[14px] pt-[14px]">
                        <p class="text-[12px] uppercase leading-4 text-gray-600">Nilai Pencairan</p>
                        <!-- TODO Backend: format angka rupiah dari kolom nilai pencairan -->
                        <p class="mt-[6px] text-[32px] font-bold leading-[38px] text-brand-800">{{ $pembayaran['nilai_pencairan'] }}</p>
                    </div>

                    <dl class="mt-[6px]">
                        <!-- MULAI LOOP: Baris informasi pembayaran (vendor, rekening, kontrak) -->
                        @foreach ($pembayaran['informasi'] as $baris)
                            <div class="flex items-center justify-between gap-4 border-b border-gray-100 py-[11px] text-[13px] last:border-b-0">
                                <dt class="text-gray-600">{{ $baris['label'] }}</dt>
                                <dd class="text-right font-bold text-gray-900">{{ $baris['nilai'] }}</dd>
                            </div>
                        @endforeach
                        <!-- AKHIR LOOP: Baris informasi pembayaran -->
                    </dl>
                </section>

                <!-- Kartu Realisasi Pembayaran: ringkasan paket + tiga isian (tanggal, jumlah, catatan) -->
                <section class="rounded-2xl border border-gray-100 bg-white px-5 pb-6 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                    <h2 class="text-[16px] font-bold leading-6">Realisasi Pembayaran</h2>

                    <!-- Ringkasan paket pembayaran (teks kecil, satu baris per keterangan) -->
                    <div class="mt-[14px] space-y-px text-[10px] leading-[13px] text-gray-700">
                        <!-- MULAI LOOP: Baris ringkasan realisasi -->
                        @foreach ($pembayaran['ringkasan_realisasi'] as $teks)
                            <p>{{ $teks }}</p>
                        @endforeach
                        <!-- AKHIR LOOP: Baris ringkasan realisasi -->
                    </div>

                    <label for="tanggal_pembayaran" class="mb-[10px] mt-[18px] block text-[11px] font-medium leading-4 text-gray-800">Tanggal Pembayaran *</label>
                    <input id="tanggal_pembayaran" name="tanggal_pembayaran" type="text" required value="{{ $pembayaran['tanggal_pembayaran'] }}"
                           class="h-[50px] w-full rounded-lg border border-dashed border-gray-300 bg-[#fafbfc] px-4 text-[11px] text-gray-900 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20">

                    <label for="jumlah_dibayar" class="mb-[10px] mt-[18px] block text-[11px] font-medium leading-4 text-gray-800">Jumlah Dibayar *</label>
                    <input id="jumlah_dibayar" name="jumlah_dibayar" type="text" inputmode="numeric" required value="{{ $pembayaran['jumlah_dibayar'] }}"
                           class="h-[50px] w-full rounded-lg border border-dashed border-gray-300 bg-[#fafbfc] px-4 text-[11px] text-gray-900 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20">

                    <label for="catatan" class="mb-[10px] mt-[18px] block text-[11px] font-medium leading-4 text-gray-800">Catatan</label>
                    <!-- Catatan opsional: kosong secara default -->
                    <textarea id="catatan" name="catatan" rows="2" placeholder="Tambahkan catatan pembayaran (opsional)..."
                              class="h-[50px] w-full resize-none rounded-lg border border-dashed border-gray-300 bg-[#fafbfc] px-4 py-[17px] text-[11px] text-gray-900 placeholder-gray-500 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20"></textarea>
                </section>
            </div>

            <!-- ---------- KOLOM KANAN: Dokumen Pendukung + Konfirmasi Realisasi Bayar ---------- -->
            <div class="space-y-6">

                <!-- Kartu Dokumen Pendukung Tambahan (PPBJ): satu baris per dokumen dengan tautan LIHAT -->
                <section class="rounded-2xl border border-gray-100 bg-white px-5 pb-5 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                    <h2 class="text-[15px] font-bold leading-6">Dokumen Pendukung Tambahan (PPBJ)</h2>

                    <ul class="mt-4 space-y-2">
                        <!-- MULAI LOOP: Dokumen pendukung dari PPBJ (satu iterasi = satu dokumen) -->
                        @foreach ($pembayaran['dokumen_pendukung'] as $dokumen)
                            <li class="flex h-[47px] items-center gap-3 rounded-lg bg-[#f1f2f4] px-3">
                                <svg class="h-[17px] w-[17px] shrink-0 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikonBerkas }}" /></svg>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[12px] font-bold leading-4">{{ $dokumen['nama'] }}</p>
                                    <p class="truncate text-[10px] leading-[14px] text-gray-500">{{ $dokumen['keterangan'] }}</p>
                                </div>
                                <!-- TODO Backend: arahkan tautan ke route pratinjau dokumen -->
                                <a href="#" class="text-[11px] font-bold uppercase text-tosca-400 transition hover:text-tosca-600">Lihat</a>
                            </li>
                        @endforeach
                        <!-- AKHIR LOOP: Dokumen pendukung -->
                    </ul>
                </section>

                <!-- Kartu Konfirmasi Realisasi Bayar: zona unggah bukti transfer, pernyataan, dan tombol kirim -->
                <section class="rounded-2xl border border-gray-100 bg-white px-5 pb-5 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                    <h2 class="text-[15px] font-bold leading-6">Konfirmasi Realisasi Bayar</h2>

                    <!-- Zona unggah: garis putus-putus; mendukung tarik-dan-lepas (drag and drop) maupun klik untuk memilih berkas -->
                    <label for="bukti_transfer" id="zona-bukti"
                           class="mt-4 flex h-[117px] cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white text-center transition hover:bg-[#f7fbfb] focus-within:ring-2 focus-within:ring-tosca-600/30">
                        <svg class="h-5 w-5 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                        <span id="nama-berkas-bukti" class="mt-[10px] max-w-full truncate px-3 text-[12px] font-bold leading-4">Bukti Transfer / Bukti Pencairan</span>
                        <span class="mt-[6px] text-[10px] leading-3 text-gray-500">Format PDF/JPG, maks 5MB</span>
                        <input id="bukti_transfer" name="bukti_transfer" type="file" accept=".pdf,.jpg,.jpeg" class="sr-only">
                    </label>

                    <!-- Pesan kesalahan: tersembunyi sampai berkas tidak valid / pernyataan belum dicentang -->
                    <p id="galat-bukti" class="mt-2 hidden items-center gap-1.5 rounded-md border border-[#f5c2c2] bg-[#fef2f2] px-2.5 py-[7px] text-[11px] font-semibold leading-4 text-[#e5484d]" role="alert"></p>

                    <!-- Pernyataan dana: wajib dicentang sebelum bukti dikirim (SENGAJA tidak dicentang secara default) -->
                    <label class="mt-4 flex cursor-pointer items-start gap-2.5 text-[11px] leading-4 text-gray-700">
                        <input type="checkbox" name="pernyataan_dana" value="1" required class="mt-0.5 h-4 w-4 shrink-0 accent-[#0f4b5b]">
                        <span>{{ $pembayaran['pernyataan_dana'] }}</span>
                    </label>

                    <button type="submit" class="mt-[18px] h-[34px] w-full rounded-lg bg-tosca-900 text-[12px] font-semibold text-white transition hover:bg-tosca-800">Kirim Bukti Penerimaan</button>

                    <p class="mt-[18px] text-[10px] leading-[13px] text-gray-500">Konfirmasi belum tersedia: bukti pembayaran belum lengkap. Tanggal dan nomor referensi sudah diisi pada data contoh.</p>
                </section>
            </div>
        </div>
    </form>

    @push('skrip')
    <script>
        (function () {
            const form = document.getElementById('form-realisasi');
            const zona = document.getElementById('zona-bukti');
            const input = document.getElementById('bukti_transfer');
            const nama = document.getElementById('nama-berkas-bukti');
            const galat = document.getElementById('galat-bukti');
            const pernyataan = form.querySelector('input[name="pernyataan_dana"]');

            const EKSTENSI = ['pdf', 'jpg', 'jpeg'];
            const MAKS_BYTE = 5 * 1024 * 1024;
            const TEKS_AWAL = 'Bukti Transfer / Bukti Pencairan';

            const tampilkanGalat = (pesan) => { galat.textContent = pesan; galat.classList.remove('hidden'); galat.classList.add('flex'); };
            const sembunyikanGalat = () => { galat.classList.add('hidden'); galat.classList.remove('flex'); };

            // Memeriksa berkas terpilih; berkas tidak valid langsung dikosongkan
            const perbarui = () => {
                sembunyikanGalat();
                const berkas = input.files[0];
                if (!berkas) { nama.textContent = TEKS_AWAL; return; }

                const ekstensi = berkas.name.split('.').pop().toLowerCase();
                let pesan = '';
                if (!EKSTENSI.includes(ekstensi)) pesan = 'Format berkas harus PDF atau JPG.';
                else if (berkas.size > MAKS_BYTE) pesan = 'Ukuran berkas maksimal 5 MB.';

                if (pesan !== '') { input.value = ''; nama.textContent = TEKS_AWAL; tampilkanGalat(pesan); return; }
                nama.textContent = berkas.name;
            };

            input.addEventListener('change', perbarui);

            // Tarik-dan-lepas: berkas yang dijatuhkan di zona dimasukkan ke input file asli
            ['dragenter', 'dragover'].forEach((n) => zona.addEventListener(n, (e) => { e.preventDefault(); zona.classList.add('bg-[#e8f3f5]'); }));
            ['dragleave', 'drop'].forEach((n) => zona.addEventListener(n, (e) => { e.preventDefault(); zona.classList.remove('bg-[#e8f3f5]'); }));
            zona.addEventListener('drop', (e) => {
                if (e.dataTransfer.files.length === 0) return;
                input.files = e.dataTransfer.files;
                perbarui();
            });

            form.addEventListener('submit', (e) => {
                if (!input.files[0]) { e.preventDefault(); tampilkanGalat('Bukti transfer wajib diunggah.'); return; }
                if (!pernyataan.checked) { e.preventDefault(); tampilkanGalat('Pernyataan dana wajib dicentang.'); return; }

                // TODO Backend: setelah action form diisi route penyimpanan, pengiriman berjalan normal (blok ini tidak aktif).
                if (form.getAttribute('action') === '') e.preventDefault();
            });
        })();
    </script>
    @endpush
@else
    {{-- ===================== KEADAAN B (status "selesai"): DOKUMENTASI PENERIMAAN (hanya-baca) ===================== --}}

    <!-- Banner sukses hijau di bagian atas -->
    <div class="flex items-center gap-3 rounded-lg border border-[#bfe6d0] bg-[#e8f7ef] px-4 py-[13px] text-[13px] text-[#15965b]" role="status">
        <svg class="h-[17px] w-[17px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <!-- TODO Backend: isi waktu dari kolom waktu penyelesaian pembayaran -->
        <p>Pembayaran selesai pada {{ $pembayaran['waktu_selesai'] }}. Bukti bayar dan jejak audit telah tersimpan.</p>
    </div>

    <!-- Kartu Ringkasan Pengadaan (lebar penuh): judul, kode, lencana status, dan empat kolom informasi -->
    <section class="mt-6 rounded-2xl border border-gray-100 bg-white px-5 pb-5 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <h2 class="text-[15px] font-bold leading-6">Ringkasan Pengadaan</h2>

        <div class="mt-[14px] flex items-start justify-between gap-4">
            <div class="min-w-0">
                <p class="text-[17px] font-bold leading-6">{{ $pembayaran['pengadaan']['judul'] }}</p>
                <p class="text-[11px] leading-4 text-gray-500">{{ $pembayaran['pengadaan']['kode'] }}</p>
            </div>
            <span class="mt-[18px] flex shrink-0 items-center gap-1.5 rounded-full bg-[#e4f7ee] px-[10px] py-[3px] text-[11px] font-medium text-[#15965b]">
                <span class="h-[5px] w-[5px] rounded-full bg-[#15965b]"></span>{{ $pembayaran['pengadaan']['status'] }}
            </span>
        </div>

        <dl class="mt-[14px] grid grid-cols-2 gap-x-6 gap-y-3 md:grid-cols-4">
            <!-- MULAI LOOP: Kolom informasi ringkasan (unit, nilai, vendor, PPK) -->
            @foreach ($pembayaran['pengadaan']['info'] as $info)
                <div>
                    <dt class="text-[11px] leading-4 text-gray-500">{{ $info['label'] }}</dt>
                    <dd class="mt-[3px] text-[13px] leading-5 text-gray-900">{{ $info['nilai'] }}</dd>
                </div>
            @endforeach
            <!-- AKHIR LOOP: Kolom informasi ringkasan -->
        </dl>
    </section>

    <!-- Dua kolom sama lebar: Bukti Realisasi Pembayaran (kiri) dan Dokumen & Persetujuan Terkait (kanan) -->
    <div class="mt-6 grid grid-cols-1 items-start gap-6 lg:grid-cols-2">

        <!-- ---------- KOLOM KIRI: Bukti Realisasi Pembayaran ---------- -->
        <section class="rounded-2xl border border-gray-100 bg-white px-5 pb-5 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[15px] font-bold leading-6">Bukti Realisasi Pembayaran</h2>

            <!-- Kotak hijau muda berisi jumlah yang dibayarkan -->
            <div class="mt-4 rounded-lg bg-[#e6f2f1] px-4 pb-4 pt-3">
                <p class="text-[11px] leading-4 text-gray-600">Jumlah Dibayar</p>
                <p class="mt-1 text-[30px] font-bold leading-9 text-brand-800">{{ $pembayaran['bukti']['jumlah_dibayar'] }}</p>
            </div>

            <dl class="mt-4 space-y-[13px]">
                <!-- MULAI LOOP: Baris rincian bukti realisasi -->
                @foreach ($pembayaran['bukti']['rincian'] as $baris)
                    <div class="grid grid-cols-[140px_1fr] gap-3 text-[13px] leading-5">
                        <dt class="text-gray-500">{{ $baris['label'] }}</dt>
                        <dd class="font-medium text-gray-900">{{ $baris['nilai'] }}</dd>
                    </div>
                @endforeach
                <!-- AKHIR LOOP: Baris rincian bukti realisasi -->
            </dl>

            <!-- Berkas Bukti Transfer Bank dengan tautan lihat / unduh -->
            <div class="mt-5 flex h-[49px] items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-3">
                <svg class="h-[17px] w-[17px] shrink-0 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikonBerkas }}" /></svg>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[12px] font-bold leading-4">{{ $pembayaran['bukti']['berkas']['judul'] }}</p>
                    <p class="truncate text-[10px] leading-[14px] text-gray-500">{{ $pembayaran['bukti']['berkas']['keterangan'] }}</p>
                </div>
                <!-- TODO Backend: arahkan tautan ke route pratinjau/unduh bukti transfer -->
                <a href="#" download class="shrink-0 text-[11px] font-bold text-tosca-400 transition hover:text-tosca-600">Lihat / Unduh</a>
            </div>
        </section>

        <!-- ---------- KOLOM KANAN: Dokumen & Persetujuan Terkait ---------- -->
        <section class="rounded-2xl border border-gray-100 bg-white px-5 pb-5 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[15px] font-bold leading-6">Dokumen &amp; Persetujuan Terkait</h2>

            <dl class="mt-4 space-y-[11px]">
                <!-- MULAI LOOP: Baris rincian perintah dan persetujuan -->
                @foreach ($pembayaran['terkait']['rincian'] as $baris)
                    <div class="grid grid-cols-[140px_1fr] gap-3 text-[13px] leading-5">
                        <dt class="text-gray-500">{{ $baris['label'] }}</dt>
                        <dd class="font-medium text-gray-900">{{ $baris['nilai'] }}</dd>
                    </div>
                @endforeach
                <!-- AKHIR LOOP: Baris rincian perintah dan persetujuan -->
            </dl>

            <ul class="mt-[14px] space-y-3">
                <!-- MULAI LOOP: Dokumen terkait (perintah pembayaran, paket pembayaran disetujui) -->
                @foreach ($pembayaran['terkait']['berkas'] as $berkas)
                    <li class="flex h-[49px] items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-3">
                        <svg class="h-[17px] w-[17px] shrink-0 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikonBerkas }}" /></svg>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[12px] font-bold leading-4">{{ $berkas['judul'] }}</p>
                            <p class="truncate text-[10px] leading-[14px] text-gray-500">{{ $berkas['keterangan'] }}</p>
                        </div>
                        <a href="#" download class="shrink-0 text-[11px] font-bold text-tosca-400 transition hover:text-tosca-600">Lihat / Unduh</a>
                    </li>
                @endforeach
                <!-- AKHIR LOOP: Dokumen terkait -->
            </ul>

            <p class="mt-3 text-[10px] leading-[14px] text-gray-500">Realisasi dan dokumen final terkunci. Koreksi hanya melalui proses berwenang dengan alasan dan audit.</p>
        </section>
    </div>

    <!-- Catatan kaki halaman -->
    <p class="mt-6 text-[10px] leading-4 text-gray-500">SIMPATIK-PBJ · Tahun Anggaran 2026 · Akses dan tindakan mengikuti kewenangan peran.</p>
@endif

@endsection
