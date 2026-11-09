{{--
    Modal Memo Persetujuan (Wadir 2 - Permohonan MAK): unggah berkas memo persetujuan PPK.
    Pemakaian: letakkan <x-wadir2.modal-memo /> DI DALAM <form id="form-mak" enctype="multipart/form-data">,
    sehingga berkas memo terkirim bersama field Form Alokasi Anggaran (mata_anggaran_kegiatan, jumlah_alokasi) dalam satu permintaan POST.
    Dibuka oleh tombol apa pun yang memakai atribut data-buka-modal="modal-memo".

    Props:
      action : alamat tujuan form. Kosong = belum ada route, pengiriman dicegah oleh skrip di bawah
--}}
@props(['action' => ''])

<!-- ===================== MODAL MEMO PERSETUJUAN (disembunyikan secara default) ===================== -->
<div id="modal-memo" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/20" role="dialog" aria-modal="true" aria-labelledby="judul-modal-memo">
    <div class="flex min-h-full items-start justify-center px-4 pb-8 pt-[150px]">
        <div class="w-full max-w-[368px] rounded-2xl border border-gray-100 bg-white px-5 pb-5 pt-[18px] shadow-xl">

            <!-- Kepala modal: ikon, judul, subjudul, dan tombol tutup (X) -->
            <div class="flex items-start justify-between border-b border-gray-200 pb-[10px]">
                <div class="flex items-start gap-2.5">
                    <svg class="mt-0.5 h-[18px] w-[18px] shrink-0 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 14v4.75A2.25 2.25 0 0117.25 21h-11A2.25 2.25 0 014 18.75v-11A2.25 2.25 0 016.25 5.5H11" />
                    </svg>
                    <div>
                        <h2 id="judul-modal-memo" class="text-[15px] font-bold leading-5">Memo Persetujuan</h2>
                        <p class="text-[11px] leading-4 text-gray-500">Lakukan pengisian memo persetujuan PPK</p>
                    </div>
                </div>
                <button type="button" data-tutup-modal aria-label="Tutup" class="text-gray-600 transition hover:text-gray-900">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>

            {{--
                TODO Backend: terima berkas "memo_persetujuan" (PDF, XLSX, JPG; maksimal 10 MB) dan validasi ulang tipe serta ukuran di server.
                Simpan berkas (mis. Storage::putFile), tandai permohonan MAK berstatus selesai, lalu tampilkan sebagai "Memo_Peretujuan.pdf" pada Keputusan Persetujuan.
            --}}

            <!-- Zona unggah: garis putus-putus; mendukung tarik-dan-lepas (drag and drop) maupun pilih file -->
            <div id="zona-memo" class="mt-4 rounded-xl border border-dashed border-tosca-600 bg-[#f7fbfb] px-[16px] pb-[14px] pt-[17px] text-center transition">
                <svg class="mx-auto h-[22px] w-[22px] text-brand-800" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                </svg>
                <p class="mt-[7px] text-[13px] font-bold leading-5">Unggah Persetujuan</p>
                <p class="mt-[7px] text-[10px] leading-4 text-gray-500">Tarik berkas atau pilih file &middot; PDF, XLSX, JPG &middot; Maks 10 MB</p>

                <!-- Kolom pilih berkas: input file asli disembunyikan, tampilannya diwakili kotak berisi nama berkas -->
                <label for="memo_persetujuan" class="mt-[10px] block text-left text-[11px] font-semibold leading-4 text-gray-800">Berkas</label>
                <label for="memo_persetujuan" class="mt-[2px] flex h-[34px] cursor-pointer items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 text-left text-[12px] text-gray-800 transition hover:bg-gray-50 focus-within:ring-2 focus-within:ring-tosca-600/30">
                    <svg class="h-3 w-3 shrink-0 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                    <span id="nama-berkas-memo" class="truncate">Pilih file untuk diunggah</span>
                    <input id="memo_persetujuan" name="memo_persetujuan" type="file" accept=".pdf,.xlsx,.jpg,.jpeg" class="sr-only">
                </label>
            </div>

            <!-- Pesan kesalahan validasi berkas: tersembunyi sampai ada berkas yang tidak valid atau belum dipilih -->
            <p id="galat-memo" class="mt-2 hidden items-center gap-1.5 rounded-md border border-[#f5c2c2] bg-[#fef2f2] px-2.5 py-[7px] text-[11px] font-semibold leading-4 text-[#e5484d]" role="alert">
                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
                <span id="teks-galat-memo"></span>
            </p>

            <p class="mt-[14px] text-[10px] leading-4 text-gray-500">Belum ada dokumen hasil. Kontrak belum diterbitkan pada tahap ini.</p>

            <!-- Tombol kirim: mengirim form MAK beserta berkas memo -->
            <button id="kirim-memo" type="submit" class="mt-3 h-[34px] w-full rounded-lg bg-tosca-900 text-[12px] font-semibold text-white transition hover:bg-tosca-800">Kirim Lembar Persetujuan</button>
        </div>
    </div>
</div>

@push('skrip')
<script>
    (function () {
        const modal = document.getElementById('modal-memo');
        const form = modal.closest('form');
        const zona = document.getElementById('zona-memo');
        const input = document.getElementById('memo_persetujuan');
        const namaBerkas = document.getElementById('nama-berkas-memo');
        const galat = document.getElementById('galat-memo');
        const teksGalat = document.getElementById('teks-galat-memo');

        const EKSTENSI = ['pdf', 'xlsx', 'jpg', 'jpeg'];
        const MAKS_BYTE = 10 * 1024 * 1024;
        const TEKS_AWAL = 'Pilih file untuk diunggah';

        const tampilkanGalat = (pesan) => { teksGalat.textContent = pesan; galat.classList.remove('hidden'); galat.classList.add('flex'); };
        const sembunyikanGalat = () => { galat.classList.add('hidden'); galat.classList.remove('flex'); };
        const buka = () => { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };
        const tutup = () => { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); sembunyikanGalat(); };

        // Mengembalikan pesan kesalahan untuk berkas yang tidak valid, atau '' jika valid
        const periksaBerkas = (berkas) => {
            const ekstensi = berkas.name.split('.').pop().toLowerCase();
            if (!EKSTENSI.includes(ekstensi)) return 'Format berkas harus PDF, XLSX, atau JPG.';
            if (berkas.size > MAKS_BYTE) return 'Ukuran berkas maksimal 10 MB.';
            return '';
        };

        // Menampilkan nama berkas terpilih; berkas tidak valid langsung dikosongkan
        const perbaruiTampilan = () => {
            sembunyikanGalat();
            const berkas = input.files[0];
            if (!berkas) { namaBerkas.textContent = TEKS_AWAL; return; }

            const pesan = periksaBerkas(berkas);
            if (pesan !== '') { input.value = ''; namaBerkas.textContent = TEKS_AWAL; tampilkanGalat(pesan); return; }
            namaBerkas.textContent = berkas.name;
        };

        // Tombol dengan data-buka-modal membuka modal; tombol X, Esc, atau klik di luar kotak menutupnya
        document.querySelectorAll('[data-buka-modal="modal-memo"]').forEach((el) => el.addEventListener('click', buka));
        modal.querySelectorAll('[data-tutup-modal]').forEach((el) => el.addEventListener('click', tutup));
        modal.addEventListener('click', (e) => { if (e.target === modal || e.target === modal.firstElementChild) tutup(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) tutup(); });

        input.addEventListener('change', perbaruiTampilan);

        // Tarik-dan-lepas: berkas yang dijatuhkan di zona dimasukkan ke input file asli
        ['dragenter', 'dragover'].forEach((nama) => zona.addEventListener(nama, (e) => {
            e.preventDefault();
            zona.classList.add('bg-[#e8f3f5]');
        }));
        ['dragleave', 'drop'].forEach((nama) => zona.addEventListener(nama, (e) => {
            e.preventDefault();
            zona.classList.remove('bg-[#e8f3f5]');
        }));
        zona.addEventListener('drop', (e) => {
            if (e.dataTransfer.files.length === 0) return;
            input.files = e.dataTransfer.files;
            perbaruiTampilan();
        });

        form.addEventListener('submit', (e) => {
            // Berkas memo wajib dipilih sebelum form dikirim
            if (!input.files[0]) {
                e.preventDefault();
                tampilkanGalat('Berkas memo wajib dipilih.');
                return;
            }

            // Field Form Alokasi Anggaran yang masih kosong: tutup modal agar pesan validasi browser terlihat
            if (!form.checkValidity()) {
                e.preventDefault();
                tutup();
                form.reportValidity();
                return;
            }

            // TODO Backend: setelah action form diisi route penerbitan memo, pengiriman berjalan normal (blok ini tidak aktif).
            @if ($action === '')
            e.preventDefault();
            @endif
        });
    })();
</script>
@endpush
