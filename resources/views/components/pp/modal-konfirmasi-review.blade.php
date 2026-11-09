{{--
    Modal "Konfirmasi Review" (PP): konfirmasi bahwa PP telah mereview pengadaan.
    Dibuka oleh tombol apa pun yang memakai atribut data-buka-modal="modal-konfirmasi-review".

    Props:
      pengadaanId : id pengadaan (dikirim lewat input tersembunyi)
      teks        : kalimat konfirmasi yang tampil di modal (disusun oleh halaman dari data pengadaan)
      action      : alamat tujuan form. Kosong = belum ada route, pengiriman dicegah oleh skrip di bawah
--}}
@props(['pengadaanId', 'teks', 'action' => ''])

<!-- ===================== MODAL KONFIRMASI REVIEW (disembunyikan secara default) ===================== -->
<div id="modal-konfirmasi-review" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/20" role="dialog" aria-modal="true" aria-labelledby="judul-modal-konfirmasi-review">
    <div class="flex min-h-full items-center justify-center px-4 py-8">
        <div class="w-full max-w-[657px] rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-xl">

            <!-- Kepala modal: judul dan tombol tutup (X) -->
            <div class="flex items-center justify-between">
                <h2 id="judul-modal-konfirmasi-review" class="text-[18px] font-bold leading-6">Konfirmasi Review</h2>
                <button type="button" data-tutup-modal aria-label="Tutup" class="text-gray-600 transition hover:text-gray-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>

            {{-- TODO Backend: isi action dengan route konfirmasi review; terima field pengadaan_id. Tanggal dan nama reviewer diambil dari server, bukan dari form. --}}
            <form method="POST" action="{{ $action }}">
                @csrf
                <input type="hidden" name="pengadaan_id" value="{{ $pengadaanId }}">

                <!-- Teks konfirmasi: tanggal, nama reviewer, dan pengadaan yang direview -->
                <p class="mt-[18px] text-[14px] leading-[22px] text-gray-800">{{ $teks }}</p>

                <!-- Tombol aksi modal: Batal (menutup modal) dan Konfirmasi (mengirim form) -->
                <div class="mt-5 flex justify-end gap-3">
                    <button type="button" data-tutup-modal
                            class="h-10 rounded-lg border border-gray-200 bg-white px-[18px] text-[13px] font-semibold text-brand-800 transition hover:bg-gray-50">Batal</button>
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
        const modal = document.getElementById('modal-konfirmasi-review');
        const form = modal.querySelector('form');

        const buka = () => { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };
        const tutup = () => { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); };

        // Tombol dengan data-buka-modal membuka modal; tombol X, Batal, Esc, atau klik di luar kotak menutupnya
        document.querySelectorAll('[data-buka-modal="modal-konfirmasi-review"]').forEach((el) => el.addEventListener('click', buka));
        modal.querySelectorAll('[data-tutup-modal]').forEach((el) => el.addEventListener('click', tutup));
        modal.addEventListener('click', (e) => { if (e.target === modal || e.target === modal.firstElementChild) tutup(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) tutup(); });

        @if ($action === '')
        // TODO Backend: setelah action form diisi route konfirmasi, pengiriman berjalan normal (blok ini tidak aktif).
        form.addEventListener('submit', (e) => { e.preventDefault(); tutup(); });
        @endif
    })();
</script>
@endpush
