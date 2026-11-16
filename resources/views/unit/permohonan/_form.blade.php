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
    $nilaiAnggaranAwal = preg_replace(
    '/\D+/',
    '',
    (string) old('nilai_anggaran', $data['nilai_anggaran'] ?? '')
);
@endphp

<!-- ===================== KARTU FORMULIR USULAN PENGADAAN UNIT ===================== -->
<section class="rounded-2xl border border-gray-100 bg-white p-[31px] shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
    <h2 class="mb-5 text-[18px] font-bold leading-6">Formulir Usulan Pengadaan Unit</h2>

    {{--
        TODO Backend: isi action dengan route penyimpanan (mis. route permohonan.store / permohonan.update).
        Untuk revisi, tambahkan @method('PUT') di dalam form. enctype multipart/form-data wajib karena ada unggah berkas.
        Tombol "Simpan Draf" dan "Kirim Permohonan" membawa name="aksi" (nilai: draf / kirim) agar backend tahu tombol mana yang ditekan.
    --}}
    <form
    id="form-permohonan"
    method="POST"
    action="{{ isset($data['id'])
        ? route('unit.permohonan.update', $data['id'])
        : route('unit.permohonan.store') }}"
    enctype="multipart/form-data"
    class="space-y-[18px]"
>
    @csrf

    @if (isset($data['id']))
        @method('PUT')
    @endif

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
            
{{-- Grup isian: Nilai Anggaran --}}
<div>
    <label for="nilai_anggaran_display" class="{{ $kelasLabel }}">
        Nilai Anggaran
    </label>

    <div class="flex h-[41px] w-full overflow-hidden rounded-lg border border-gray-200 bg-white transition focus-within:border-tosca-400 focus-within:ring-2 focus-within:ring-tosca-400/20">

        <span class="flex items-center border-r border-gray-200 bg-gray-50 px-3 text-sm font-medium text-gray-600">
            Rp
        </span>

        {{-- Input yang dilihat dan diketik pengguna --}}
        <input
            id="nilai_anggaran_display"
            type="text"
            inputmode="numeric"
            autocomplete="off"
            value="{{ $nilaiAnggaranAwal !== '' ? number_format((int) $nilaiAnggaranAwal, 0, ',', '.') : '' }}"
            placeholder="Contoh: 200.000.000"
            class="h-full w-full border-0 bg-transparent px-3 text-[14px] text-gray-900 outline-none"
        >

        {{-- Nilai asli yang dikirim ke backend --}}
        <input
            type="hidden"
            id="nilai_anggaran"
            name="nilai_anggaran"
            value="{{ $nilaiAnggaranAwal }}"
        >
    </div>

    @error('nilai_anggaran')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>


        <!-- Baris 3: tujuan dan alasan kebutuhan (area teks, lebar penuh) -->
        <div>
            <label for="alasan_kebutuhan" class="{{ $kelasLabel }}">Tujuan &amp; Alasan Kebutuhan Pengadaan *</label>
            <textarea id="alasan_kebutuhan" name="alasan_kebutuhan" rows="4" required placeholder="Jelaskan urgensi, deskripsi singkat, dan perkiraan manfaat dari pengadaan ini..."
                      class="{{ $kelasInput }} h-[99px] resize-y py-2.5">{{ old('alasan_kebutuhan', $data['deskripsi'] ?? '') }}</textarea>
        </div>

        

{{-- Baris 4: unggah tiga kategori dokumen --}}
<div class="space-y-4">
    <div>
        <span class="{{ $kelasLabel }}">
            Unggah Lampiran Pendukung (RAB, KAK, Spesifikasi)
        </span>
        <p class="mt-1 text-xs text-gray-500">
            Lampiran boleh belum lengkap saat menyimpan draf,
            tetapi wajib lengkap saat mengirim permohonan.
        </p>
    </div>

    @foreach ([
        'rab' => 'Dokumen RAB',
        'kak' => 'Dokumen KAK',
        'spesifikasi' => 'Dokumen Spesifikasi',
    ] as $field => $label)
        <div class="space-y-2">
            <label for="{{ $field }}" class="{{ $kelasLabel }}">
                {{ $label }}
            </label>

            <label
                id="zona-{{ $field }}"
                for="{{ $field }}"
                class="flex min-h-[100px] cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-4 text-center transition hover:border-blue-400 hover:bg-blue-50"
            >
                <svg
                    class="mb-2 h-7 w-7 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M12 16V4m0 0L8 8m4-4 4 4M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3"
                    />
                </svg>

                <span class="text-sm font-medium text-gray-700">
                    Klik atau tarik file ke sini
                </span>

                <span
                    id="nama-{{ $field }}"
                    class="mt-1 text-xs text-gray-500"
                >
                    Belum ada file dipilih
                </span>

                <input
                    id="{{ $field }}"
                    name="{{ $field }}"
                    type="file"
                    accept=".pdf,.xlsx,.zip"
                    class="sr-only"
                >
                @if (!empty($data['dokumen'][$field]))
    <p class="mt-2 text-xs text-green-700">
        <span class="font-semibold">Dokumen tersimpan:</span>
        {{ basename($data['dokumen'][$field]->File_path) }}
    </p>
@else
    <p class="mt-2 text-xs text-gray-400">
        Belum ada dokumen tersimpan.
    </p>
@endif
            </label>

            @error($field)
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endforeach

    <p class="text-xs text-gray-500">
        Format PDF, XLSX, atau ZIP. Maksimal 10 MB per file.
    </p>
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
    ['rab', 'kak', 'spesifikasi'].forEach(field => {
        const zona = document.getElementById(`zona-${field}`);
        const input = document.getElementById(field);
        const nama = document.getElementById(`nama-${field}`);

        if (!zona || !input || !nama) return;

        const tampilkanBerkas = () => {
            nama.textContent = input.files.length
                ? input.files[0].name
                : 'Belum ada file dipilih';
        };

        input.addEventListener('change', tampilkanBerkas);

        ['dragenter', 'dragover'].forEach(eventName => {
            zona.addEventListener(eventName, event => {
                event.preventDefault();
                zona.classList.add('border-blue-500', 'bg-blue-50');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            zona.addEventListener(eventName, event => {
                event.preventDefault();
                zona.classList.remove('border-blue-500', 'bg-blue-50');
            });
        });

        zona.addEventListener('drop', event => {
            input.files = event.dataTransfer.files;
            tampilkanBerkas();
        });
    });

    // Format otomatis nominal Rupiah.
const inputNominal = document.getElementById('nilai_anggaran_display');
const nominalAsli = document.getElementById('nilai_anggaran');
const formPermohonan = document.getElementById('form-permohonan');

if (inputNominal && nominalAsli) {

    const formatRupiah = (angka) => {
        const angkaBersih = angka.replace(/\D/g, '');

        return angkaBersih.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    inputNominal.addEventListener('input', function () {

        const angkaBersih = this.value.replace(/\D/g, '');

        this.value = formatRupiah(angkaBersih);

        // Hidden input menyimpan angka tanpa titik.
        nominalAsli.value = angkaBersih;
    });

    // Sinkronisasi ketika form dikirim.
    if (formPermohonan) {
        formPermohonan.addEventListener('submit', function () {
            nominalAsli.value = inputNominal.value.replace(/\D/g, '');
        });
    }
}
</script>
@endpush

