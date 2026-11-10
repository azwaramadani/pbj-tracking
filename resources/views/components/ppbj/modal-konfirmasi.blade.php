{{--
    Modal konfirmasi umum PPBJ (disembunyikan secara default). Dipasang DI DALAM form yang dikonfirmasi, sehingga tombol "Konfirmasi"
    (type="submit") mengirim form tersebut. Dibuka oleh tombol apa pun dengan atribut data-buka-modal-ppbj="<id modal>".
    Dipakai oleh "Kirim Untuk Persetujuan" (Unggah Dokumen Pembayaran) dan "Terbitkan Perintah" (Persetujuan Paket Pembayaran).

    Props:
      id        : id elemen modal (dipasangkan dengan data-buka-modal-ppbj pada tombol pembuka)
      judul     : judul modal
      pesan     : kalimat konfirmasi di bawah judul
      ringkasan : baris ringkasan, tiap item [label, isi]
--}}
@props(['id', 'judul', 'pesan', 'ringkasan' => []])

<!-- ===================== MODAL: {{ $judul }} ===================== -->
<div id="{{ $id }}" data-modal-ppbj class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/20" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-judul">
    <div class="flex min-h-full items-start justify-center px-4 pb-8 pt-[150px]">
        <div class="w-full max-w-[462px] rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">

            <!-- Kepala modal: judul dan tombol tutup (X) -->
            <div class="flex items-center justify-between">
                <h2 id="{{ $id }}-judul" class="text-[18px] font-bold leading-6">{{ $judul }}</h2>
                <button type="button" data-tutup-modal-ppbj aria-label="Tutup" class="text-gray-600 transition hover:text-gray-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>

            <p class="mt-3 text-[13px] leading-5 text-gray-500">{{ $pesan }}</p>

            <!-- Ringkasan informasi yang dikonfirmasi -->
            <dl class="mt-4 space-y-[10px] rounded-lg border border-gray-200 bg-[#f8fafc] px-4 py-[14px] text-[13px] leading-5">
                <!-- MULAI LOOP: Baris ringkasan (satu iterasi = satu pasangan label-isi) -->
                @foreach ($ringkasan as [$label, $isi])
                    <div class="grid grid-cols-[130px_1fr] gap-x-3">
                        <dt class="text-gray-500">{{ $label }}</dt>
                        <dd class="font-bold text-gray-900">{{ $isi }}</dd>
                    </div>
                @endforeach
                <!-- AKHIR LOOP: Baris ringkasan -->
            </dl>

            <!-- Tombol aksi modal: Batal (menutup modal) dan Konfirmasi (mengirim form pembungkus) -->
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" data-tutup-modal-ppbj
                        class="h-10 rounded-lg border border-gray-200 bg-white px-[18px] text-[13px] font-semibold text-gray-500 transition hover:bg-gray-50">Batal</button>
                <button type="submit" data-konfirmasi-modal-ppbj
                        class="h-10 rounded-lg bg-tosca-900 px-5 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

@once
    @push('skrip')
    <script>
        // Modal PPBJ: buka lewat data-buka-modal-ppbj; tutup lewat X, Batal, Esc, atau klik di luar kotak
        (function () {
            const buka = (modal) => { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };
            const tutup = (modal) => { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); };

            document.querySelectorAll('[data-buka-modal-ppbj]').forEach((tombol) => tombol.addEventListener('click', () => {
                buka(document.getElementById(tombol.dataset.bukaModalPpbj));
            }));

            document.querySelectorAll('[data-modal-ppbj]').forEach((modal) => {
                modal.querySelectorAll('[data-tutup-modal-ppbj]').forEach((el) => el.addEventListener('click', () => tutup(modal)));
                modal.addEventListener('click', (e) => { if (e.target === modal || e.target === modal.firstElementChild) tutup(modal); });

                // TODO Backend: setelah action form diisi route penyimpanan, pengiriman berjalan normal (blok ini tidak aktif).
                modal.querySelector('[data-konfirmasi-modal-ppbj]').addEventListener('click', (e) => {
                    const form = e.currentTarget.closest('form');
                    if (form && !form.getAttribute('action')) { e.preventDefault(); tutup(modal); }
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') document.querySelectorAll('[data-modal-ppbj]:not(.hidden)').forEach(tutup);
            });
        })();
    </script>
    @endpush
@endonce
