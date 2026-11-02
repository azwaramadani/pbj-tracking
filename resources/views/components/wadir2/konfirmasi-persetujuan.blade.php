{{--
    Modal Konfirmasi Persetujuan (Wadir 2) - dipakai oleh halaman Detail Permohonan dan Persetujuan Paket Pembayaran.
    Pemakaian: <x-wadir2.konfirmasi-persetujuan :record-id="$id" field-name="permohonan_id" :action="route('...')" />
    Dibuka oleh tombol apa pun yang memakai atribut data-buka-modal="modal-konfirmasi".

    Props:
      recordId  : id permohonan / paket yang diputuskan (dikirim lewat input tersembunyi)
      fieldName : nama input tersembunyi untuk id di atas (permohonan_id | paket_id)
      action    : alamat tujuan form. Kosong = belum ada route, pengiriman dicegah oleh skrip di bawah
--}}
@props(['recordId', 'fieldName' => 'permohonan_id', 'action' => ''])

<!-- ===================== MODAL KONFIRMASI PERSETUJUAN (disembunyikan secara default) ===================== -->
<div id="modal-konfirmasi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/20" role="dialog" aria-modal="true" aria-labelledby="judul-modal-konfirmasi">
    <div class="flex min-h-full items-start justify-center px-4 pb-8 pt-[150px]">
        <div class="w-full max-w-[462px] rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">

            <!-- Kepala modal: judul dan tombol tutup (X) -->
            <div class="flex items-center justify-between">
                <h2 id="judul-modal-konfirmasi" class="text-[18px] font-bold leading-6">Konfirmasi Persetujuan</h2>
                <button type="button" data-tutup-modal aria-label="Tutup" class="text-gray-600 transition hover:text-gray-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>

            {{--
                TODO Backend: isi action dengan route penyimpanan keputusan dan terima field:
                id permohonan/paket (sesuai fieldName), keputusan (setuju | revisi | tolak), alasan_persetujuan.
                Alasan WAJIB diisi bila keputusan = tolak (validasi ulang di server; pesan: "Alasan penolakan wajib diisi.").
            --}}
            <form id="form-konfirmasi" method="POST" action="{{ $action }}" novalidate>
                @csrf
                <input type="hidden" name="{{ $fieldName }}" value="{{ $recordId }}">

                <!-- Pilihan keputusan: tiga kartu radio; kartu yang dipilih diberi garis dan latar tosca -->
                <fieldset class="mt-[22px]">
                    <legend class="text-[12px] font-semibold uppercase leading-4 text-gray-600">Keputusan</legend>
                    <div class="mt-[10px] grid grid-cols-3 gap-2">
                        <!-- MULAI LOOP: Opsi keputusan (nilai dikirim ke backend lewat name="keputusan") -->
                        @foreach (['setuju' => 'Setuju', 'revisi' => 'Revisi', 'tolak' => 'Tolak'] as $nilai => $label)
                            <label class="flex h-10 cursor-pointer items-center gap-2 rounded-lg border border-gray-200 bg-[#f6f8fa] px-3 text-[13px] font-semibold text-gray-700 transition has-[:checked]:border-tosca-900 has-[:checked]:bg-[#e8f3f5] has-[:checked]:text-brand-800">
                                <input type="radio" name="keputusan" value="{{ $nilai }}" class="h-4 w-4 accent-[#0f4b5b]" @checked($nilai === 'setuju')>
                                {{ $label }}
                            </label>
                        @endforeach
                        <!-- AKHIR LOOP: Opsi keputusan -->
                    </div>
                </fieldset>

                <!-- Alasan persetujuan: SENGAJA KOSONG secara default (tanpa isi awal dan tanpa placeholder) -->
                <div class="mt-5">
                    <label for="alasan_persetujuan" class="block text-[12px] font-semibold uppercase leading-4 text-gray-600">Alasan Persetujuan <span class="text-[#e5484d]">*</span></label>
                    <textarea id="alasan_persetujuan" name="alasan_persetujuan" rows="4"
                              class="mt-[9px] h-[96px] w-full resize-none rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-[13px] outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20"></textarea>

                    <!-- Petunjuk aturan pengisian (selalu tampil) -->
                    <p class="mt-[6px] flex items-center gap-1.5 text-[11px] leading-4 text-[#e5484d]">
                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
                        Wajib diisi jika permohonan ditolak.
                    </p>

                    <!-- Pesan kesalahan validasi: tersembunyi sampai pengguna memilih Tolak dengan alasan kosong lalu menekan Konfirmasi -->
                    <p id="galat-alasan" class="mt-2 hidden items-center gap-1.5 rounded-md border border-[#f5c2c2] bg-[#fef2f2] px-2.5 py-[7px] text-[11px] font-semibold leading-4 text-[#e5484d]" role="alert">
                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
                        Alasan penolakan wajib diisi.
                    </p>
                </div>

                <!-- Tombol aksi modal: Batal (menutup modal) dan Konfirmasi (mengirim form) -->
                <div class="mt-5 flex justify-end gap-3">
                    <button type="button" data-tutup-modal
                            class="h-10 rounded-lg border border-gray-200 bg-white px-[18px] text-[13px] font-semibold text-gray-500 transition hover:bg-gray-50">Batal</button>
                    <button type="submit"
                            class="h-10 rounded-lg bg-tosca-900 px-5 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('skrip')
<script>
    (function () {
        const modal = document.getElementById('modal-konfirmasi');
        const form = document.getElementById('form-konfirmasi');
        const alasan = document.getElementById('alasan_persetujuan');
        const galat = document.getElementById('galat-alasan');

        const sembunyikanGalat = () => { galat.classList.add('hidden'); galat.classList.remove('flex'); };
        const buka = () => { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };
        const tutup = () => { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); sembunyikanGalat(); };

        // Tombol dengan data-buka-modal membuka modal; tombol X, Batal, Esc, atau klik di luar kotak menutupnya
        document.querySelectorAll('[data-buka-modal="modal-konfirmasi"]').forEach((el) => el.addEventListener('click', buka));
        modal.querySelectorAll('[data-tutup-modal]').forEach((el) => el.addEventListener('click', tutup));
        modal.addEventListener('click', (e) => { if (e.target === modal || e.target === modal.firstElementChild) tutup(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) tutup(); });

        // Pesan kesalahan hilang begitu pengguna mengubah pilihan atau mengetik alasan
        alasan.addEventListener('input', sembunyikanGalat);
        form.querySelectorAll('input[name="keputusan"]').forEach((el) => el.addEventListener('change', sembunyikanGalat));

        // Validasi sisi klien: alasan wajib diisi jika keputusan = Tolak
        form.addEventListener('submit', (e) => {
            const keputusan = form.querySelector('input[name="keputusan"]:checked').value;

            if (keputusan === 'tolak' && alasan.value.trim() === '') {
                e.preventDefault();
                galat.classList.remove('hidden');
                galat.classList.add('flex');
                alasan.focus();
                return;
            }

            // TODO Backend: setelah action form diisi route penyimpanan, pengiriman berjalan normal (blok ini tidak aktif).
            @if ($action === '')
            e.preventDefault();
            @endif
        });
    })();
</script>
@endpush
