{{--
    Zona unggah berkas (kotak putus-putus): ikon awan, judul, petunjuk, lalu kolom "Berkas". Berkas dapat dipilih lewat klik ATAU diseret (drag-and-drop).
    Dipakai oleh Metode Pengadaan, Unggah Kontrak & SPK, dan Unggah Dokumen Pembayaran.

    Props:
      name     : nama field file yang dikirim ke backend
      judul    : teks tebal di tengah zona (mis. "Unggah Surat Kontrak / SPK / Surat Pesanan")
      wajib    : true = berkas wajib diisi (atribut required)
      accept   : tipe berkas yang diizinkan (validasi ulang tipe dan ukuran maks 10 MB di server)
--}}
@props(['name', 'judul', 'wajib' => false, 'accept' => '.pdf,.xlsx,.jpg,.jpeg'])

<div data-zona-unggah class="rounded-lg border border-dashed border-tosca-400 bg-[#f3f9fb] px-5 py-5 text-center transition data-[seret]:bg-[#e2f2f4]">
    <svg class="mx-auto h-6 w-6 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" /></svg>
    <p class="mt-2 text-[13px] font-bold leading-5">{{ $judul }}</p>
    <p class="mt-1 text-[10px] leading-4 text-gray-500">Tarik berkas atau pilih file · PDF, XLSX, JPG · Maksimal 10 MB</p>

    <p class="mt-3 text-left text-[11px] font-medium leading-4 text-gray-700">Berkas</p>
    <label class="mt-1.5 flex h-10 cursor-pointer items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 text-left text-[13px] text-gray-800 transition hover:border-tosca-400">
        <svg class="h-4 w-4 shrink-0 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
        <span data-nama-berkas class="truncate">Pilih file untuk diunggah</span>
        <input type="file" name="{{ $name }}" accept="{{ $accept }}" @required($wajib) class="sr-only">
    </label>
</div>

@once
    @push('skrip')
    <script>
        // Zona unggah: nama berkas tampil setelah dipilih; berkas yang diseret ke zona dimasukkan ke input file
        document.querySelectorAll('[data-zona-unggah]').forEach((zona) => {
            const input = zona.querySelector('input[type="file"]');
            const nama = zona.querySelector('[data-nama-berkas]');
            const bawaan = nama.textContent;
            const tampilkan = () => { nama.textContent = input.files.length ? input.files[0].name : bawaan; };

            input.addEventListener('change', tampilkan);
            ['dragenter', 'dragover'].forEach((e) => zona.addEventListener(e, (ev) => { ev.preventDefault(); zona.setAttribute('data-seret', ''); }));
            ['dragleave', 'drop'].forEach((e) => zona.addEventListener(e, (ev) => { ev.preventDefault(); zona.removeAttribute('data-seret'); }));
            zona.addEventListener('drop', (ev) => {
                if (ev.dataTransfer.files.length) { input.files = ev.dataTransfer.files; tampilkan(); }
            });
        });
    </script>
    @endpush
@endonce
