
{{-- 
    Modal Detail/Edit generik Super Admin.

    Perilaku:
    - Awalnya semua isian dalam mode baca.
    - Tombol Edit membuka isian.
    - Tombol Simpan Perubahan mengirim form PUT.
    - Tombol Hapus sementara dinonaktifkan untuk Manajemen Pengguna.
    - Tombol Tutup, X, Escape, atau klik di luar modal menutup modal.
--}}

@props(['objek', 'judul', 'fields', 'id' => 'modal-detail-admin'])

{{-- MODAL DETAIL --}}
<div
    id="{{ $id }}"
    data-modal-detail-admin
    data-mode="baca"
    class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/20"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}-judul"
>
    <div class="flex min-h-full items-start justify-center px-4 pb-8 pt-[72px]">

        {{-- FORM DETAIL / EDIT --}}
        <form
            method="POST"
            action=""
            class="w-full max-w-[735px] overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl"
        >
            @csrf
            <input
                type="hidden"
                name="_method"
                value="PUT"
                data-metode-detail-admin
            >

            {{-- KEPALA MODAL --}}
            <div class="flex items-start justify-between border-b border-gray-100 px-6 pb-3 pt-5">
                <div>
                    <h2
                        id="{{ $id }}-judul"
                        class="text-[20px] font-bold leading-7"
                    >
                        Detail {{ $objek }}
                    </h2>

                    <p class="text-[12px] leading-4 text-gray-500">
                        {{ $judul }}
                    </p>
                </div>

                <button
                    type="button"
                    data-tutup-detail-admin
                    aria-label="Tutup"
                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-[#f4f6f8] text-gray-600 transition hover:bg-gray-100"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 6l12 12M18 6L6 18"
                        />
                    </svg>
                </button>
            </div>

            {{-- ISI MODAL --}}
            <div class="grid grid-cols-1 gap-x-5 gap-y-4 px-6 py-6 sm:grid-cols-2">

                @foreach ($fields as $f)
                    @php
                        $idIsian = $id . '-' . $f['kunci'];
                        $namaField = $f['nama'] ?? $f['kunci'];

                        $kelasIsian = 'mt-1.5 block w-full rounded-md border border-transparent bg-transparent px-1 py-0.5 text-[13px] font-bold text-gray-900 outline-none transition disabled:cursor-default disabled:appearance-none disabled:opacity-100 disabled:[-webkit-text-fill-color:#111827] enabled:border-gray-300 enabled:bg-white enabled:focus:border-tosca-600 enabled:focus:ring-2 enabled:focus:ring-tosca-600/20';
                    @endphp

                    <div class="rounded-xl border border-gray-100 bg-[#fbfdff] px-4 py-[14px] shadow-[0_1px_2px_rgba(15,23,42,0.03)]">

                        <label
                            for="{{ $idIsian }}"
                            class="block text-[10px] font-semibold uppercase leading-3 text-gray-500"
                        >
                            {{ $f['label'] }}
                        </label>

                        {{-- ISIAN TETAP --}}
                        @if (!empty($f['tetap']))

                            <input
                                type="hidden"
                                name="{{ $namaField }}"
                                data-isi="{{ $f['kunci'] }}"
                            >

                            <input
                                type="text"
                                id="{{ $idIsian }}"
                                data-isi="{{ $f['kunci'] }}"
                                data-tetap
                                disabled
                                class="{{ $kelasIsian }}"
                            >

                        {{-- DROPDOWN --}}
                        @elseif (!empty($f['opsi']))

                            <select
                                id="{{ $idIsian }}"
                                name="{{ $namaField }}"
                                data-isi="{{ $f['kunci'] }}"
                                data-edit
                                disabled
                                class="{{ $kelasIsian }}"
                            >
                                @foreach ($f['opsi'] as $opsi)

                                    @if (is_array($opsi))
                                        <option value="{{ $opsi['value'] }}">
                                            {{ $opsi['label'] }}
                                        </option>
                                    @else
                                        <option value="{{ $opsi }}">
                                            {{ $opsi }}
                                        </option>
                                    @endif

                                @endforeach
                            </select>

                        {{-- INPUT BIASA --}}
                        @else

                            <input
                                type="{{ $f['jenis'] ?? 'text' }}"
                                id="{{ $idIsian }}"
                                name="{{ $namaField }}"
                                data-isi="{{ $f['kunci'] }}"
                                data-edit
                                disabled
                                class="{{ $kelasIsian }}"
                            >

                        @endif

                    </div>
                @endforeach

            </div>

            {{-- TOMBOL AKSI --}}
            <div class="flex justify-end gap-3 border-t border-gray-100 px-6 py-4">

                <button
                    type="button"
                    data-tutup-detail-admin
                    class="h-[38px] rounded-lg border border-gray-200 bg-white px-8 text-[13px] font-semibold text-gray-700 transition hover:bg-gray-50"
                >
                    Tutup
                </button>

                <button
                    type="button"
                    data-hapus-detail-admin
                    class="h-[38px] rounded-lg border border-gray-200 bg-white px-8 text-[13px] font-semibold text-gray-700 transition hover:border-[#f2b8b8] hover:bg-[#fff5f5] hover:text-[#d93a3a]"
                >
                    Hapus
                </button>

                <button
                    type="button"
                    data-edit-detail-admin
                    data-label-edit="Edit {{ $objek }}"
                    class="h-[38px] rounded-lg bg-tosca-900 px-6 text-[13px] font-semibold text-white transition hover:bg-tosca-800"
                >
                    Edit {{ $objek }}
                </button>

            </div>
        </form>

    </div>
</div>

@once
    @push('skrip')
        <script>
            (function () {

                document.querySelectorAll('[data-modal-detail-admin]').forEach((modal) => {

                    const form = modal.querySelector('form');
                    const tombolEdit = modal.querySelector('[data-edit-detail-admin]');
                    const tombolHapus = modal.querySelector('[data-hapus-detail-admin]');
                    const isianEdit = modal.querySelectorAll('[data-edit]');

                    // Mengatur mode baca dan edit.
                    const aturMode = (edit) => {

                        modal.dataset.mode = edit ? 'edit' : 'baca';

                        isianEdit.forEach((el) => {
                            el.disabled = !edit;
                        });

                        tombolEdit.textContent = edit
                            ? 'Simpan Perubahan'
                            : tombolEdit.dataset.labelEdit;

                        if (edit && isianEdit.length) {
                            isianEdit[0].focus();
                        }
                    };

                    // Membuka modal.
                    const buka = () => {
                        modal.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    };

                    // Menutup modal dan mengembalikan mode baca.
                    const tutup = () => {
                        aturMode(false);
                        modal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    };

                    // Mengisi data ke dalam modal.
                    const isi = (data) => {

                        modal.querySelectorAll('[data-isi]').forEach((el) => {

                            const nilai = data[el.dataset.isi] ?? '';

                            if (el.tagName === 'SELECT') {

                                const nilaiString = String(nilai);

                                if (![...el.options].some((option) => option.value === nilaiString)) {
                                    el.add(new Option(nilaiString, nilaiString));
                                }

                                el.value = nilaiString;

                            } else {

                                el.value = nilai;

                            }

                        });

                    };

                    // Membuka detail dari tombol pada tabel.
                    document.querySelectorAll('[data-buka-detail-admin]').forEach((tombol) => {

                        tombol.addEventListener('click', () => {

                            const data = JSON.parse(tombol.dataset.detail);

                            isi(data);

                            // Mengambil URL update dari tombol Detail.
                            form.action = tombol.dataset.updateUrl || '';

                            // Mengembalikan metode form ke PUT.
                            form.querySelector('[data-metode-detail-admin]').value = 'PUT';

                            aturMode(false);

                            buka();

                        });

                    });

                    // Tombol Tutup.
                    modal.querySelectorAll('[data-tutup-detail-admin]').forEach((el) => {
                        el.addEventListener('click', tutup);
                    });

                    // Klik area luar modal.
                    modal.addEventListener('click', (e) => {

                        if (
                            e.target === modal ||
                            e.target === modal.firstElementChild
                        ) {
                            tutup();
                        }

                    });

                    // Tombol Edit dan Simpan Perubahan.
                    tombolEdit.addEventListener('click', () => {

                        // Klik pertama: masuk mode edit.
                        if (modal.dataset.mode !== 'edit') {
                            aturMode(true);
                            return;
                        }

                        // Cegah pengiriman jika URL update belum tersedia.
                        if (!form.getAttribute('action')) {
                            alert('URL update belum dikonfigurasi.');
                            return;
                        }

                        // Klik kedua: kirim perubahan.
                        form.requestSubmit();

                    });

                    // Tombol Hapus.
                    tombolHapus.addEventListener('click', () => {

                        // Jika URL tersedia, berarti modal sedang terhubung
                        // dengan halaman Manajemen Pengguna.
                        if (form.getAttribute('action')) {
                            alert('Fitur hapus pengguna belum diaktifkan.');
                            return;
                        }

                        // Perilaku halaman dummy lainnya tetap dipertahankan.
                        if (window.confirm('Hapus data ini?')) {
                            tutup();
                        }

                    });

                    // Tombol Escape.
                    document.addEventListener('keydown', (e) => {

                        if (
                            e.key === 'Escape' &&
                            !modal.classList.contains('hidden')
                        ) {
                            tutup();
                        }

                    });

                });

            })();
        </script>
    @endpush
@endonce
