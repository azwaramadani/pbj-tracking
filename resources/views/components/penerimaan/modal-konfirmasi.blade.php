{{--
    Modal Konfirmasi Bukti Penerimaan - dipasang DI DALAM form penerimaan (id="form-penerimaan"), sehingga tombol "Konfirmasi"
    (type="submit") mengirim seluruh isian, foto, dan lampiran. Dibuka oleh skrip halaman setelah isian dinyatakan valid.

    Props:
      nomorSpk : nomor SPK penerimaan yang dikonfirmasi

    Ringkasan memakai placeholder data-ringkasan="..." yang diisi skrip dari isian form: nama, unit, tanggal, total_item.
--}}
@props(['nomorSpk'])

<!-- ===================== MODAL KONFIRMASI BUKTI PENERIMAAN (disembunyikan secara default) ===================== -->
<div id="modal-penerimaan" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/20" role="dialog" aria-modal="true" aria-labelledby="judul-modal-penerimaan">
    <div class="flex min-h-full items-start justify-center px-4 pb-8 pt-[150px]">
        <div class="w-full max-w-[462px] rounded-2xl border border-gray-100 bg-white p-6 shadow-xl">

            <!-- Kepala modal: judul dan tombol tutup (X) -->
            <div class="flex items-center justify-between">
                <h2 id="judul-modal-penerimaan" class="text-[18px] font-bold leading-6">Konfirmasi Bukti Penerimaan</h2>
                <button type="button" data-tutup-modal aria-label="Tutup" class="text-gray-600 transition hover:text-gray-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>

            <p class="mt-3 text-[13px] leading-5 text-gray-500">Pastikan data berikut sudah benar. Setelah dikonfirmasi, penerimaan barang dicatat dan statusnya berubah menjadi Telah Diterima.</p>

            <!-- Ringkasan isian: placeholder diisi otomatis oleh skrip halaman (data-ringkasan) -->
            <dl class="mt-4 space-y-[10px] rounded-lg border border-gray-200 bg-[#f8fafc] px-4 py-[14px] text-[13px] leading-5">
                <div class="grid grid-cols-[130px_1fr] gap-x-3">
                    <dt class="text-gray-500">Nomor SPK</dt>
                    <dd class="font-bold text-gray-900">{{ $nomorSpk }}</dd>
                </div>
                <div class="grid grid-cols-[130px_1fr] gap-x-3">
                    <dt class="text-gray-500">Nama Penerima</dt>
                    <dd class="font-bold text-gray-900" data-ringkasan="nama">-</dd>
                </div>
                <div class="grid grid-cols-[130px_1fr] gap-x-3">
                    <dt class="text-gray-500">Unit / Bagian</dt>
                    <dd class="font-bold text-gray-900" data-ringkasan="unit">-</dd>
                </div>
                <div class="grid grid-cols-[130px_1fr] gap-x-3">
                    <dt class="text-gray-500">Tanggal Penerimaan</dt>
                    <dd class="font-bold text-gray-900" data-ringkasan="tanggal">-</dd>
                </div>
                <div class="grid grid-cols-[130px_1fr] gap-x-3">
                    <dt class="text-gray-500">Total Item</dt>
                    <dd class="font-bold text-gray-900" data-ringkasan="total_item">-</dd>
                </div>
            </dl>

            <!-- Tombol aksi modal: Batal (menutup modal) dan Konfirmasi (mengirim form penerimaan) -->
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" data-tutup-modal
                        class="h-10 rounded-lg border border-gray-200 bg-white px-[18px] text-[13px] font-semibold text-gray-500 transition hover:bg-gray-50">Batal</button>
                <button type="submit"
                        class="h-10 rounded-lg bg-tosca-900 px-5 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>
