{{--
    Modal Detail/Edit generik Super Admin (disembunyikan secara default). Contoh pada Manajemen Pengguna: "Detail Pengguna".
    Dibuka oleh tombol apa pun dengan atribut data-buka-detail-admin dan data-detail='{"kunci":"nilai",...}' (data satu baris).

    Perilaku:
      - Awalnya semua isian disabled (hanya-baca). Tombol "Edit {objek}" membuka isian (kecuali yang bertanda 'tetap', mis. ID)
        dan mengubah teks tombol menjadi "Simpan Perubahan". Klik "Simpan Perubahan" mengirim form.
      - "Hapus" meminta konfirmasi lalu mengirim form dengan _method=DELETE. "Tutup", tombol X, Esc, atau klik di luar kotak menutup modal
        dan mengembalikan modal ke mode hanya-baca.
      - TODO Backend: isi action form ini (mis. route('admin.pengguna.update', $id)). Selama action kosong, pengiriman diblokir (hanya demo tampilan).

    Props:
      objek  : nama tunggal (mis. "Pengguna") untuk judul "Detail Pengguna" dan tombol "Edit Pengguna"
      judul  : subjudul di bawah judul modal (nama halaman, mis. "Manajemen Pengguna")
      fields : isian, tiap item ['label', 'kunci' (nama kolom & atribut name), 'jenis' => text|email (opsional),
               'opsi' => daftar pilihan dropdown (opsional), 'tetap' => true bila tidak boleh diubah (opsional)]
      id     : id elemen modal
--}}
@props(['objek', 'judul', 'fields', 'id' => 'modal-detail-admin'])

<!-- ===================== MODAL: Detail {{ $objek }} ===================== -->
<div id="{{ $id }}" data-modal-detail-admin data-mode="baca" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/20" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-judul">
    <div class="flex min-h-full items-start justify-center px-4 pb-8 pt-[72px]">

        {{-- Form pembungkus: @csrf dan _method (PUT untuk simpan, DIHAPUS lewat tombol Hapus) --}}
        <form method="POST" action="" class="w-full max-w-[735px] overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl">
            @csrf
            <input type="hidden" name="_method" value="PUT" data-metode-detail-admin>

            <!-- Kepala modal: judul, subjudul, dan tombol tutup (X) -->
            <div class="flex items-start justify-between border-b border-gray-100 px-6 pb-3 pt-5">
                <div>
                    <h2 id="{{ $id }}-judul" class="text-[20px] font-bold leading-7">Detail {{ $objek }}</h2>
                    <p class="text-[12px] leading-4 text-gray-500">{{ $judul }}</p>
                </div>
                <button type="button" data-tutup-detail-admin aria-label="Tutup"
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-[#f4f6f8] text-gray-600 transition hover:bg-gray-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>

            <!-- Isi modal: kartu isian 2 kolom, terisi dari data baris yang dipilih (atribut data-isi) -->
            <div class="grid grid-cols-1 gap-x-5 gap-y-4 px-6 py-6 sm:grid-cols-2">
                <!-- MULAI LOOP: Isian detail (satu iterasi = satu kartu). Nama isian = kolom database -->
                @foreach ($fields as $f)
                    @php
                        $idIsian = $id.'-'.$f['kunci'];
                        $kelasIsian = 'mt-1.5 block w-full rounded-md border border-transparent bg-transparent px-1 py-0.5 text-[13px] font-bold text-gray-900 outline-none transition disabled:cursor-default disabled:appearance-none disabled:opacity-100 disabled:[-webkit-text-fill-color:#111827] enabled:border-gray-300 enabled:bg-white enabled:focus:border-tosca-600 enabled:focus:ring-2 enabled:focus:ring-tosca-600/20';
                    @endphp
                    <div class="rounded-xl border border-gray-100 bg-[#fbfdff] px-4 py-[14px] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">
                        <label for="{{ $idIsian }}" class="block text-[10px] font-semibold uppercase leading-3 text-gray-500">{{ $f['label'] }}</label>

                        @if (! empty($f['tetap']))
                            {{-- Isian tetap: tampil disabled, nilainya tetap ikut terkirim lewat input tersembunyi --}}
                            <input type="hidden" name="{{ $f['kunci'] }}" data-isi="{{ $f['kunci'] }}">
                            <input type="text" id="{{ $idIsian }}" data-isi="{{ $f['kunci'] }}" data-tetap disabled class="{{ $kelasIsian }}">
                        @elseif (! empty($f['opsi']))
                            <select id="{{ $idIsian }}" name="{{ $f['kunci'] }}" data-isi="{{ $f['kunci'] }}" data-edit disabled class="{{ $kelasIsian }}">
                                @foreach ($f['opsi'] as $opsi)
                                    <option value="{{ $opsi }}">{{ $opsi }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="{{ $f['jenis'] ?? 'text' }}" id="{{ $idIsian }}" name="{{ $f['kunci'] }}" data-isi="{{ $f['kunci'] }}" data-edit disabled class="{{ $kelasIsian }}">
                        @endif
                    </div>
                @endforeach
                <!-- AKHIR LOOP: Isian detail -->
            </div>

            <!-- Tombol aksi: Tutup, Hapus, dan Edit {{ $objek }} (berubah menjadi "Simpan Perubahan" saat mode edit) -->
            <div class="flex justify-end gap-3 border-t border-gray-100 px-6 py-4">
                <button type="button" data-tutup-detail-admin
                        class="h-[38px] rounded-lg border border-gray-200 bg-white px-8 text-[13px] font-semibold text-gray-700 transition hover:bg-gray-50">Tutup</button>
                <button type="button" data-hapus-detail-admin
                        class="h-[38px] rounded-lg border border-gray-200 bg-white px-8 text-[13px] font-semibold text-gray-700 transition hover:border-[#f2b8b8] hover:bg-[#fff5f5] hover:text-[#d93a3a]">Hapus</button>
                <button type="button" data-edit-detail-admin data-label-edit="Edit {{ $objek }}"
                        class="h-[38px] rounded-lg bg-tosca-900 px-6 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Edit {{ $objek }}</button>
            </div>
        </form>
    </div>
</div>

@once
    @push('skrip')
    <script>
        // Modal Detail/Edit Super Admin: buka lewat data-buka-detail-admin; mode baca <-> edit lewat tombol Edit/Simpan Perubahan
        (function () {
            document.querySelectorAll('[data-modal-detail-admin]').forEach((modal) => {
                const form = modal.querySelector('form');
                const tombolEdit = modal.querySelector('[data-edit-detail-admin]');
                const isianEdit = modal.querySelectorAll('[data-edit]');

                // Mode edit: buka/kunci isian (isian bertanda data-tetap tidak pernah dibuka) dan ganti teks tombol
                const aturMode = (edit) => {
                    modal.dataset.mode = edit ? 'edit' : 'baca';
                    isianEdit.forEach((el) => { el.disabled = !edit; });
                    tombolEdit.textContent = edit ? 'Simpan Perubahan' : tombolEdit.dataset.labelEdit;
                    if (edit && isianEdit.length) isianEdit[0].focus();
                };

                const buka = () => { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };
                const tutup = () => { aturMode(false); modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); };

                // Isi semua kartu dari data baris; pilihan dropdown yang belum ada ditambahkan agar nilai tetap tampil
                const isi = (data) => {
                    modal.querySelectorAll('[data-isi]').forEach((el) => {
                        const nilai = data[el.dataset.isi] ?? '';
                        if (el.tagName === 'SELECT' && ![...el.options].some((o) => o.value === nilai)) el.add(new Option(nilai, nilai));
                        el.value = nilai;
                    });
                };

                document.querySelectorAll('[data-buka-detail-admin]').forEach((tombol) => tombol.addEventListener('click', () => {
                    isi(JSON.parse(tombol.dataset.detail));
                    form.querySelector('[data-metode-detail-admin]').value = 'PUT';
                    aturMode(false);
                    buka();
                }));

                modal.querySelectorAll('[data-tutup-detail-admin]').forEach((el) => el.addEventListener('click', tutup));
                modal.addEventListener('click', (e) => { if (e.target === modal || e.target === modal.firstElementChild) tutup(); });

                // Edit Pengguna -> Simpan Perubahan: klik pertama membuka isian, klik kedua mengirim form
                tombolEdit.addEventListener('click', () => {
                    if (modal.dataset.mode !== 'edit') { aturMode(true); return; }
                    // TODO Backend: setelah action form diisi route update, pengiriman berjalan normal (blok ini tidak aktif).
                    if (!form.getAttribute('action')) { aturMode(false); return; }
                    form.requestSubmit();
                });

                // Hapus: konfirmasi dahulu, lalu kirim form sebagai DELETE
                modal.querySelector('[data-hapus-detail-admin]').addEventListener('click', () => {
                    if (!window.confirm('Hapus data ini?')) return;
                    if (!form.getAttribute('action')) { tutup(); return; }
                    form.querySelector('[data-metode-detail-admin]').value = 'DELETE';
                    form.submit();
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) tutup();
                });
            });
        })();
    </script>
    @endpush
@endonce
