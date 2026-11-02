{{--
    Formulir Usulan Pengadaan Unit - dipakai bersama oleh halaman Buat Permohonan dan Revisi Pengajuan.

    Variabel (opsional):
      $permohonan : data permohonan yang sudah ada. Bila diisi (mode revisi), kolom otomatis terisi nilai lama;
                    bila kosong (mode buat baru), kolom tampil kosong dengan teks contoh.
--}}

@php
    /* Kelas Tailwind bersama untuk semua kolom isian agar tampilannya seragam */
    $kelasInput = 'h-[41px] w-full rounded-lg border border-gray-200 bg-white px-3 text-[14px] text-gray-900 placeholder-gray-500 outline-none transition focus:border-tosca-400 focus:ring-2 focus:ring-tosca-400/20';
    $kelasLabel = 'mb-1.5 block text-[14px] font-semibold text-gray-900';
    $data = $permohonan ?? [];
@endphp

<!-- ===================== KARTU FORMULIR USULAN PENGADAAN UNIT ===================== -->
<section class="rounded-2xl border border-gray-100 bg-white p-[31px] shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
    <h2 class="mb-5 text-[18px] font-bold leading-6">Formulir Usulan Pengadaan Unit</h2>

    {{--
        TODO Backend: isi action dengan route penyimpanan (mis. route permohonan.store / permohonan.update).
        Untuk revisi, tambahkan @method('PUT') di dalam form. enctype multipart/form-data wajib karena ada unggah berkas.
        Tombol "Simpan Draf" dan "Kirim Permohonan" membawa name="aksi" (nilai: draf / kirim) agar backend tahu tombol mana yang ditekan.
    --}}
    <form id="form-permohonan" method="POST" action="" enctype="multipart/form-data" class="space-y-[18px]">
        @csrf

        <!-- Baris 1: dua kolom sejajar (Nomor Surat dan Judul Pengadaan) -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <!-- Grup isian: Nomor Surat Resmi Unit -->
            <div>
                <label for="nomor_surat" class="{{ $kelasLabel }}">Nomor Surat Resmi Unit *</label>
                <input id="nomor_surat" name="nomor_surat" type="text" required value="{{ old('nomor_surat', $data['no_surat'] ?? '') }}"
                       placeholder="Contoh: 021/UN1.FT/PBJ/2026" class="{{ $kelasInput }}">
            </div>

            <!-- Grup isian: Judul Pengadaan / Perihal -->
            <div>
                <label for="judul_pengadaan" class="{{ $kelasLabel }}">Judul Pengadaan / Perihal *</label>
                <input id="judul_pengadaan" name="judul_pengadaan" type="text" required value="{{ old('judul_pengadaan', $data['judul'] ?? '') }}"
                       placeholder="Masukkan perihal pengadaan barang / jasa" class="{{ $kelasInput }}">
            </div>
        </div>

        <!-- Baris 2: dua kolom sejajar (Asal Unit Pengusul dan Nilai Anggaran) -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <!-- Grup isian: pilihan unit pengusul. TODO Backend: isi pilihan dari tabel unit -->
            <div>
                <label for="asal_unit" class="{{ $kelasLabel }}">Asal Unit Pengusul *</label>
                <div class="relative">
                    <select id="asal_unit" name="asal_unit" required class="{{ $kelasInput }} appearance-none pr-10">
                        <option value="FT-02" selected>Fakultas Teknik - Perencanaan &amp; IT (FT-02)</option>
                    </select>
                    <!-- Ikon panah bawah untuk dropdown -->
                    <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-900" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </div>

            <!-- Grup isian: Nilai Anggaran (tidak wajib). TODO Backend: bersihkan format rupiah sebelum disimpan sebagai angka -->
            <div>
                <label for="nilai_anggaran" class="{{ $kelasLabel }}">Nilai Anggaran</label>
                <input id="nilai_anggaran" name="nilai_anggaran" type="text" inputmode="numeric" value="{{ old('nilai_anggaran', $data['nilai_anggaran'] ?? '') }}"
                       placeholder="Masukkan perihal nilai anggaran" class="{{ $kelasInput }}">
            </div>
        </div>

        <!-- Baris 3: tujuan dan alasan kebutuhan (area teks, lebar penuh) -->
        <div>
            <label for="alasan_kebutuhan" class="{{ $kelasLabel }}">Tujuan &amp; Alasan Kebutuhan Pengadaan *</label>
            <textarea id="alasan_kebutuhan" name="alasan_kebutuhan" rows="4" required placeholder="Jelaskan urgensi, deskripsi singkat, dan perkiraan manfaat dari pengadaan ini..."
                      class="{{ $kelasInput }} h-[99px] resize-y py-2.5">{{ old('alasan_kebutuhan', $data['deskripsi'] ?? '') }}</textarea>
        </div>

        <!-- Baris 4: area unggah lampiran dengan fitur drag-and-drop -->
        <div>
            <span class="{{ $kelasLabel }}">Unggah Lampiran Pendukung (RAB, KAK, Spesifikasi) *</span>

            <!-- Zona drag-and-drop: seluruh area bisa diklik; garis putus-putus berubah saat file diseret ke atasnya -->
            <label id="zona-unggah" for="lampiran"
                   class="flex h-[144px] cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-tosca-400 bg-[#f8fafc] px-4 text-center transition">
                <!-- Ikon awan dengan panah unggah -->
                <svg class="h-[30px] w-[30px] text-tosca-400" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                </svg>
                <span class="mt-3.5 text-[14px] font-semibold">Tarik dan lepaskan file di sini, atau klik tombol jelajahi</span>
                <span class="mt-1 text-[12px] text-gray-500">Hanya file PDF, XLSX, atau ZIP (Maksimal 10MB per file)</span>
                <!-- Daftar nama file yang sudah dipilih (diisi oleh skrip di bawah) -->
                <span id="daftar-berkas" class="mt-1 text-[12px] font-medium text-tosca-400"></span>

                <!-- Input file asli disembunyikan secara visual; name berupa array agar bisa banyak berkas -->
                <input id="lampiran" name="lampiran[]" type="file" multiple accept=".pdf,.xlsx,.zip" class="sr-only">
            </label>
        </div>

        <!-- Baris tombol aksi di kanan bawah: Simpan Draf (outline) dan Kirim Permohonan (tosca gelap) -->
        <div class="flex justify-end gap-3 !pt-[14px]">
            <button type="submit" name="aksi" value="draf"
                    class="h-[41px] rounded-lg border border-gray-200 bg-white px-6 text-[14px] font-semibold text-gray-500 transition hover:bg-gray-50">Simpan Draf</button>
            <button type="submit" name="aksi" value="kirim"
                    class="h-[41px] rounded-lg bg-tosca-900 px-6 text-[14px] font-semibold text-white transition hover:bg-tosca-800">Kirim Permohonan</button>
        </div>
    </form>
</section>

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
        zona.classList.add('bg-tosca-400/5', 'border-solid');
    }));

    ['dragleave', 'drop'].forEach(nama => zona.addEventListener(nama, e => {
        e.preventDefault();
        zona.classList.remove('bg-tosca-400/5', 'border-solid');
    }));

    // Saat file dilepas, masukkan ke input file agar ikut terkirim bersama form
    zona.addEventListener('drop', e => {
        input.files = e.dataTransfer.files;
        tampilkanBerkas();
    });

    // TODO Backend: hapus baris ini setelah action form diisi route penyimpanan. Selama belum ada route, pengiriman dicegah.
    document.getElementById('form-permohonan').addEventListener('submit', e => e.preventDefault());
</script>
@endpush
