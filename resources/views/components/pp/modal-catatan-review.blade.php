{{--
    Modal "Catatan Review" (PP) / "Catatan Perbaikan" (PPK): pengguna menuliskan catatan untuk pengadaan.
    Dibuka oleh tombol apa pun yang memakai atribut data-buka-modal="modal-catatan-review".

    Props:
      pengadaanId : id pengadaan (dikirim lewat input tersembunyi)
      action      : alamat tujuan form. Kosong = belum ada route, pengiriman dicegah oleh skrip di bawah
      judul       : judul modal (default "Catatan Review"; PPK memakai "Catatan Perbaikan")
      namaField   : atribut name pada textarea (default "catatan"; PPK memakai "catatan_perbaikan")
--}}
@props(['pengadaanId', 'action' => '', 'judul' => 'Catatan Review', 'namaField' => 'catatan'])

<!-- ===================== MODAL CATATAN REVIEW (disembunyikan secara default) ===================== -->
<div id="modal-catatan-review" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/20" role="dialog" aria-modal="true" aria-labelledby="judul-modal-catatan-review">
    <div class="flex min-h-full items-center justify-center px-4 py-8">
        <div class="w-full max-w-[480px] rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">

            <!-- Kepala modal: judul dan tombol tutup (X) -->
            <div class="flex items-center justify-between">
                <h2 id="judul-modal-catatan-review" class="text-[18px] font-bold leading-6">{{ $judul }}</h2>
                <button type="button" data-tutup-modal aria-label="Tutup" class="text-gray-600 transition hover:text-gray-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>

            {{-- TODO Backend: isi action dengan route penyimpanan catatan; terima field pengadaan_id dan isian catatan (nama field sesuai prop namaField) --}}
            <form method="POST" action="{{ $action }}">
                @csrf
                <input type="hidden" name="pengadaan_id" value="{{ $pengadaanId }}">

                <!-- Isian catatan: kosong secara default; placeholder sesuai mockup -->
                <label for="{{ $namaField }}" class="sr-only">{{ $judul }}</label>
                <textarea id="{{ $namaField }}" name="{{ $namaField }}" rows="4" placeholder="Masukan note..."
                          class="mt-[18px] h-[100px] w-full resize-none rounded-lg border border-tosca-600 bg-white px-3 py-2.5 text-[13px] text-gray-900 placeholder-gray-700 outline-none transition focus:ring-2 focus:ring-tosca-600/20"></textarea>

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
        const modal = document.getElementById('modal-catatan-review');
        const form = modal.querySelector('form');

        const buka = () => { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); modal.querySelector('textarea').focus(); };
        const tutup = () => { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); };

        // Tombol dengan data-buka-modal membuka modal; tombol X, Batal, Esc, atau klik di luar kotak menutupnya
        document.querySelectorAll('[data-buka-modal="modal-catatan-review"]').forEach((el) => el.addEventListener('click', buka));
        modal.querySelectorAll('[data-tutup-modal]').forEach((el) => el.addEventListener('click', tutup));
        modal.addEventListener('click', (e) => { if (e.target === modal || e.target === modal.firstElementChild) tutup(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) tutup(); });

        @if ($action === '')
        // TODO Backend: setelah action form diisi route penyimpanan, pengiriman berjalan normal (blok ini tidak aktif).
        form.addEventListener('submit', (e) => { e.preventDefault(); tutup(); });
        @endif
    })();
</script>
@endpush
