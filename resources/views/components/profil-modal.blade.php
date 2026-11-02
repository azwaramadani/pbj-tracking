{{--
    Modal Edit Profil universal (disembunyikan secara default) - dipakai semua role.
    Pemakaian: <x-profil-modal :user="$user" :action="route('...profil.update')" />
    Dibuka oleh elemen apa pun yang memakai atribut data-buka-modal="modal-edit-profil"; alamat dengan #edit juga membukanya.

    Props:
      user   : objek/model pengguna. Properti yang dibaca: name, nip, email, phone, unit, institution, role
      action : alamat tujuan form (route pembaruan profil). Kosong = belum ada route, pengiriman dicegah oleh skrip di bawah
--}}
@props(['user', 'action' => ''])

<!-- ===================== MODAL EDIT PROFIL (disembunyikan secara default) ===================== -->
<div id="modal-edit-profil" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/20" role="dialog" aria-modal="true" aria-labelledby="judul-modal-edit-profil">
    <div class="flex min-h-full items-start justify-center px-4 pb-8 pt-[75px]">
        <div class="w-full max-w-[799px] rounded-2xl border border-gray-200 bg-white px-[27px] pb-[31px] pt-[24px] shadow-xl">

            <!-- Kepala modal: judul + subjudul + garis pemisah -->
            <div class="border-b border-gray-200 pb-[13px]">
                <h2 id="judul-modal-edit-profil" class="text-[14px] font-bold leading-5">Informasi Pribadi</h2>
                <p class="text-[12.5px] leading-5 text-gray-500">Pastikan informasi yang dimasukkan sesuai data kepegawaian.</p>
            </div>

            {{--
                TODO Backend: isi action dengan route pembaruan profil (tambahkan @method('PUT') bila memakai resource route).
                enctype multipart/form-data wajib karena ada unggah foto profil. Kolom NIP, Instansi, dan Jabatan bersifat readonly;
                backend tetap harus mengabaikan nilainya agar data kepegawaian tidak bisa diubah dari form.
            --}}
            <form id="form-edit-profil" method="POST" action="{{ $action }}" enctype="multipart/form-data">
                @csrf

                <!-- Bagian foto profil: pratinjau bulat + tombol Ganti Foto dan Hapus -->
                <div class="mt-4 flex items-center gap-[18px]">
                    <div id="pratinjau-foto" class="flex h-[68px] w-[68px] shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#cce9e4]">
                        <svg class="mt-2 h-[52px] w-[52px] text-[#5fb8a8]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 12a4.5 4.5 0 100-9 4.5 4.5 0 000 9zm-8.25 9.75a8.25 8.25 0 0116.5 0v.75H3.75v-.75z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[12.5px] font-medium leading-4">Foto Profil</p>
                        <p class="mt-1 text-[12px] leading-4 text-gray-500">JPG atau PNG · Maksimal 2 MB</p>
                        <div class="mt-2 flex items-center gap-[19px]">
                            <!-- Tombol "Ganti Foto" membuka pemilih berkas (input file tersembunyi) -->
                            <label for="foto_profil" class="flex h-8 cursor-pointer items-center rounded-lg border border-[#cfe8e3] bg-[#e8f6f4] px-[28px] text-[12px] font-semibold text-tosca-400 transition hover:bg-[#dcf0ed]">Ganti Foto</label>
                            <input id="foto_profil" name="foto_profil" type="file" accept=".jpg,.jpeg,.png" class="sr-only">
                            <!-- "Hapus" mengosongkan pilihan foto dan menandai hapus_foto=1 agar backend menghapus foto lama -->
                            <input id="hapus_foto" name="hapus_foto" type="hidden" value="0">
                            <button id="tombol-hapus-foto" type="button" class="text-[12px] font-semibold text-[#b4232c] hover:underline">Hapus</button>
                        </div>
                    </div>
                </div>

                <!-- Grid isian dua kolom: Nama/NIP, Email/Telepon, Unit Kerja/Instansi -->
                <div class="mt-[38px] grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2">

                    <div>
                        <label for="nama_lengkap" class="mb-[3px] block text-[12px] uppercase text-gray-600">Nama Lengkap *</label>
                        <input id="nama_lengkap" name="nama_lengkap" type="text" required value="{{ $user->name }}"
                               class="h-11 w-full rounded-lg border border-gray-200 bg-white px-[14px] text-[13px] outline-none transition focus:border-tosca-400 focus:ring-2 focus:ring-tosca-400/20">
                    </div>

                    <!-- NIP: terkunci (readonly) dengan pil "Dikunci" di kanan -->
                    <div>
                        <label for="nip" class="mb-[3px] block text-[12px] uppercase text-gray-600">NIP *</label>
                        <div class="relative">
                            <input id="nip" name="nip" type="text" readonly value="{{ $user->nip }}"
                                   class="h-11 w-full cursor-not-allowed rounded-lg border border-gray-200 bg-[#f1f4f6] px-[14px] pr-24 text-[13px] text-gray-800 outline-none">
                            <span class="absolute right-[12px] top-1/2 -translate-y-1/2 rounded-full bg-[#e6eaee] px-[18px] py-[3px] text-[10px] font-bold text-gray-600">Dikunci</span>
                        </div>
                    </div>

                    <div>
                        <label for="email_kedinasan" class="mb-[3px] block text-[12px] uppercase text-gray-600">Email Kedinasan *</label>
                        <input id="email_kedinasan" name="email_kedinasan" type="email" required value="{{ $user->email }}"
                               class="h-11 w-full rounded-lg border border-gray-200 bg-white px-[14px] text-[13px] outline-none transition focus:border-tosca-400 focus:ring-2 focus:ring-tosca-400/20">
                    </div>

                    <div>
                        <label for="nomor_telepon" class="mb-[3px] block text-[12px] uppercase text-gray-600">Nomor Telepon *</label>
                        <input id="nomor_telepon" name="nomor_telepon" type="tel" required value="{{ $user->phone }}"
                               class="h-11 w-full rounded-lg border border-gray-200 bg-white px-[14px] text-[13px] outline-none transition focus:border-tosca-400 focus:ring-2 focus:ring-tosca-400/20">
                    </div>

                    <!-- Unit Kerja: dropdown. TODO Backend: isi pilihan dari tabel unit -->
                    <div>
                        <label for="unit_kerja" class="mb-[3px] block text-[12px] uppercase text-gray-600">Unit Kerja *</label>
                        <div class="relative">
                            <select id="unit_kerja" name="unit_kerja" required
                                    class="h-11 w-full appearance-none rounded-lg border border-gray-200 bg-white px-[14px] pr-10 text-[13px] outline-none transition focus:border-tosca-400 focus:ring-2 focus:ring-tosca-400/20">
                                <option value="{{ $user->unit }}" selected>{{ $user->unit }}</option>
                            </select>
                            <svg class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>

                    <!-- Instansi / Fakultas: terkunci -->
                    <div>
                        <label for="instansi" class="mb-[3px] block text-[12px] uppercase text-gray-600">Instansi / Fakultas *</label>
                        <div class="relative">
                            <input id="instansi" name="instansi" type="text" readonly value="{{ $user->institution }}"
                                   class="h-11 w-full cursor-not-allowed rounded-lg border border-gray-200 bg-[#f1f4f6] px-[14px] pr-24 text-[13px] text-gray-800 outline-none">
                            <span class="absolute right-[12px] top-1/2 -translate-y-1/2 rounded-full bg-[#e6eaee] px-[18px] py-[3px] text-[10px] font-bold text-gray-600">Dikunci</span>
                        </div>
                    </div>

                    <!-- Jabatan: terkunci, lebar penuh, disertai keterangan di bawahnya -->
                    <div class="md:col-span-2">
                        <label for="jabatan" class="mb-[3px] block text-[12px] uppercase text-gray-600">Jabatan</label>
                        <input id="jabatan" name="jabatan" type="text" readonly value="{{ $user->role }}"
                               class="h-11 w-full cursor-not-allowed rounded-lg border border-gray-200 bg-[#f1f4f6] px-[14px] text-[13px] text-gray-800 outline-none">
                        <p class="mt-[10px] text-[12px] leading-4 text-gray-500">Jabatan dan data kepegawaian dikelola oleh administrator.</p>
                    </div>
                </div>

                <!-- Tombol aksi modal: Batalkan (menutup modal) dan Simpan Perubahan (mengirim form) -->
                <div class="mt-[22px] flex justify-end gap-4 border-t border-gray-200 pt-[15px]">
                    <button type="button" data-tutup-modal
                            class="h-[41px] rounded-lg border border-gray-200 bg-white px-[23px] text-[14px] font-semibold text-gray-600 transition hover:bg-gray-50">Batalkan</button>
                    <button type="submit"
                            class="h-[41px] rounded-lg bg-tosca-900 px-[23px] text-[14px] font-semibold text-white transition hover:bg-tosca-800">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('skrip')
<script>
    (function () {
        const modal = document.getElementById('modal-edit-profil');
        const buka = () => { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };
        const tutup = () => { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); };

        // Tombol dengan data-buka-modal membuka modal; Batalkan, Esc, atau klik di luar kotak menutupnya
        document.querySelectorAll('[data-buka-modal="modal-edit-profil"]').forEach((el) => el.addEventListener('click', buka));
        modal.querySelectorAll('[data-tutup-modal]').forEach((el) => el.addEventListener('click', tutup));
        modal.addEventListener('click', (e) => { if (e.target === modal || e.target === modal.firstElementChild) tutup(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) tutup(); });

        // Tautan langsung: halaman dibuka dengan modal terbuka jika alamatnya berakhiran #edit
        if (location.hash === '#edit') buka();

        // Pratinjau foto: tampilkan gambar yang dipilih pada lingkaran foto; "Hapus" mengembalikan ke ikon bawaan
        const inputFoto = document.getElementById('foto_profil');
        const pratinjau = document.getElementById('pratinjau-foto');
        const hapusFoto = document.getElementById('hapus_foto');
        const isiAwal = pratinjau.innerHTML;

        inputFoto.addEventListener('change', () => {
            const berkas = inputFoto.files[0];
            if (!berkas) return;
            const gambar = document.createElement('img');
            gambar.src = URL.createObjectURL(berkas);
            gambar.className = 'h-full w-full object-cover';
            pratinjau.replaceChildren(gambar);
            hapusFoto.value = '0';
        });

        document.getElementById('tombol-hapus-foto').addEventListener('click', () => {
            inputFoto.value = '';
            pratinjau.innerHTML = isiAwal;
            hapusFoto.value = '1';
        });

        // TODO Backend: hapus baris ini setelah action form diisi route pembaruan profil. Selama belum ada route, pengiriman dicegah.
        @if ($action === '')
        document.getElementById('form-edit-profil').addEventListener('submit', (e) => e.preventDefault());
        @endif
    })();
</script>
@endpush
