@extends('layouts.'.($layoutRole ?? 'penerimaan'))

@section('judul_halaman', 'Dokumentasi Penerimaan')
@section('menu_aktif', $menuAktif ?? 'dashboard')
@section('url_kembali', $urlKembali ?? route('penerimaan.dashboard'))

@section('konten')

{{--
    Dokumentasi Penerimaan - satu view untuk dua tampilan, ditentukan oleh $penerimaan['status']:
      'dijalankan' : form "Diterima oleh" dapat diisi + tombol "Kirim Bukti Penerimaan" (membuka modal konfirmasi),
                     foto dapat ditambah/dihapus, lampiran dapat dihapus, dan dokumen serah terima diunggah
      'selesai'    : banner sukses di atas, semua isian hanya-baca, tanpa tombol kirim/unggah/hapus
    Variabel dari Penerimaan\DokumentasiController@show:
      $penerimaan : id, status, nomor_spk, nomor, banner, informasi[] (pasangan [label, isi]), status_verifikasi,
                    penerima{nama,unit,tanggal,catatan}|null, foto[] (id, label, url), lampiran[] (judul, berkas)
                    - lihat App\Support\Penerimaan\DataDokumentasi::cari()
--}}

@php
    // $tercatat = penerimaan sudah dicatat (status selesai); $selesai = MODE HANYA-BACA (status selesai ATAU $bacaSaja = true dari role lain, mis. PPBJ)
    $tercatat = $penerimaan['status'] === 'selesai';
    $selesai = $tercatat || ($bacaSaja ?? false);

    // Ikon yang dipakai berulang
    $ikonBerkas = 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 10.5l1.5 1.5 3-3M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z';
    $ikonCentang = 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    $ikonSampah = 'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0';

    // Gaya isian: putus-putus saat dapat diisi, solid dan tidak bisa diubah saat hanya-baca
    $kelasIsian = 'h-[44px] w-full rounded-lg border border-dashed border-gray-300 bg-[#fafbfc] px-4 text-[12px] text-gray-900 placeholder-gray-500 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20';
    $kelasBaca = 'h-[44px] w-full cursor-default rounded-lg border border-gray-200 bg-[#f6f8fa] px-4 text-[13px] text-gray-900 outline-none';

    // Kolom "Diterima oleh": [name, label, placeholder, kunci nilai tersimpan, wajib]
    $kolomPenerima = [
        ['nama_penerima',      'Nama *',              'Hendra Wijaya (petugas terpilih, belum disimpan)',      'nama',    true],
        ['unit_bagian',        'Unit / Bagian *',     'Perlengkapan',                                          'unit',    true],
        ['tanggal_penerimaan', 'Tanggal Penerimaan *', 'Pilih tanggal penerimaan pada 2026',                   'tanggal', true],
        ['catatan',            'Catatan',             'Kondisi, jumlah diterima, dan selisih jika ada...',     'catatan', false],
    ];
@endphp

<!-- Banner sukses hijau di bagian paling atas (hanya status "selesai") -->
@if ($tercatat)
    <div class="flex items-center gap-3 rounded-lg border border-[#bfe6d0] bg-[#e8f7ef] px-4 py-[13px] text-[13px] text-[#15965b]" role="status">
        <svg class="h-[17px] w-[17px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikonCentang }}" /></svg>
        <!-- TODO Backend: isi dari nomor dan waktu pencatatan penerimaan -->
        <p>{{ $penerimaan['banner'] }}</p>
    </div>
@endif

{{--
    TODO Backend: isi action dengan route penyimpanan penerimaan barang (mis. route('penerimaan.dokumentasi.store', $penerimaan['id'])).
    Satu form multipart membungkus kedua kolom dan modal konfirmasi, sehingga isian, foto, dan lampiran terkirim bersamaan.
    Field: penerimaan_id, nama_penerima, unit_bagian, tanggal_penerimaan, catatan, foto_barang[] (file gambar), hapus_foto[] (id foto),
    sertifikat_uji_fungsi (file pdf, opsional), dokumen_serah_terima (file pdf/jpg, wajib), hapus_lampiran[] (indeks lampiran).
    Validasi ulang di server: tipe dan ukuran berkas, tanggal pada 2026, dan nama/unit/tanggal wajib diisi.
    Pada status "selesai" tidak ada form sama sekali: halaman hanya menampilkan data tersimpan.
--}}
@unless ($selesai)
<form id="form-penerimaan" method="POST" action="" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="penerimaan_id" value="{{ $penerimaan['id'] }}">
@endunless

    <div class="mt-6 grid grid-cols-1 items-start gap-6 lg:grid-cols-2">

        <!-- ---------- KOLOM KIRI: Informasi Dokumen Kontrak & SPK + "Diterima oleh" ---------- -->
        <div class="space-y-6">

            <!-- Kartu Informasi Dokumen Kontrak & SPK: pasangan label (abu-abu) dan isi (tebal), hanya-baca -->
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-5 pt-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="flex items-center gap-2 border-b border-gray-200 pb-[14px] text-[16px] font-bold leading-6">
                    <svg class="h-5 w-5 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikonBerkas }}" /></svg>
                    Informasi Dokumen Kontrak &amp; SPK
                </h2>

                <dl class="mt-[14px] space-y-[9px]">
                    <!-- MULAI LOOP: Baris informasi dokumen kontrak dan SPK -->
                    @foreach ($penerimaan['informasi'] as [$label, $isi])
                        <div class="grid grid-cols-[160px_1fr] gap-x-3 text-[13px] leading-5">
                            <dt class="text-gray-500">{{ $label }}</dt>
                            <dd class="font-bold text-gray-900">{{ $isi }}</dd>
                        </div>
                    @endforeach
                    <!-- AKHIR LOOP: Baris informasi -->
                </dl>
            </section>

            <!-- Kartu "Diterima oleh": dapat diisi saat "dijalankan", hanya-baca (tanpa tombol kirim) saat "selesai" -->
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-[16px] font-bold leading-6">Diterima oleh</h2>
                <p class="mt-3 text-[13px] leading-5 text-gray-500">
                    @if ($tercatat)
                        Data penerima barang sudah dikonfirmasi dan tidak dapat diubah.
                    @elseif ($selesai)
                        Barang belum diterima. Data penerima akan tampil di sini setelah penerimaan dicatat.
                    @else
                        Diisi saat barang diterima. Data belum dikonfirmasi; status saat ini Menunggu Penerimaan.
                    @endif
                </p>

                <!-- MULAI LOOP: Isian penerima (Nama, Unit / Bagian, Tanggal Penerimaan, Catatan) -->
                @foreach ($kolomPenerima as [$nama, $label, $placeholder, $kunci, $wajib])
                    <label for="{{ $nama }}" class="mb-2 mt-[18px] block text-[12px] font-medium leading-4 text-gray-800">{{ $label }}</label>
                    @if ($selesai)
                        <input id="{{ $nama }}" type="text" readonly value="{{ $penerimaan['penerima'][$kunci] ?? '-' }}" class="{{ $kelasBaca }}">
                    @elseif ($nama === 'tanggal_penerimaan')
                        <!-- Berubah menjadi pemilih tanggal saat diklik; placeholder tetap terlihat selama kosong -->
                        <input id="{{ $nama }}" name="{{ $nama }}" type="text" required placeholder="{{ $placeholder }}"
                               min="2026-01-01" max="2026-12-31"
                               onfocus="this.type='date'" onblur="if(!this.value){this.type='text'}" class="{{ $kelasIsian }}">
                    @else
                        <input id="{{ $nama }}" name="{{ $nama }}" type="text" @required($wajib) placeholder="{{ $placeholder }}" class="{{ $kelasIsian }}">
                    @endif
                @endforeach
                <!-- AKHIR LOOP: Isian penerima -->

                <!-- Tombol kirim (hanya "dijalankan"): TIDAK langsung mengirim, melainkan membuka modal Konfirmasi Bukti Penerimaan -->
                @unless ($selesai)
                    <button type="button" id="tombol-kirim"
                            class="mt-6 h-10 w-full rounded-lg bg-tosca-900 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Kirim Bukti Penerimaan</button>
                @endunless
            </section>
        </div>

        <!-- ---------- KOLOM KANAN: Status Verifikasi + Foto Fisik + Lampiran Dokumen Pendukung ---------- -->
        <div class="space-y-6">

            <!-- Kartu Status Verifikasi Barang: label di kiri, lencana di kanan (kuning = menunggu, hijau = telah diterima) -->
            <section class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white px-6 py-4 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <p class="text-[13px] font-bold">Status Verifikasi Barang:</p>
                <span class="rounded-full px-3 py-1 text-[11px] font-semibold {{ $tercatat ? 'bg-[#d9f5e5] text-[#1fa35c]' : 'bg-[#fef3c7] text-[#d87705]' }}">{{ $penerimaan['status_verifikasi'] }}</span>
            </section>

            <!-- Kartu Dokumentasi Foto Fisik Barang -->
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-5 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-[14px] font-bold leading-5">Dokumentasi Foto Fisik Barang (Multi Foto)</h2>
                    @unless ($selesai)
                        <button type="button" data-pilih-foto class="shrink-0 text-[12px] font-bold text-tosca-400 transition hover:underline">Unggah Semua</button>
                    @endunless
                </div>

                <!-- Grid foto 3 kolom: foto tersimpan (dirender server); saat "dijalankan" ditambah kotak kosong "Tambah Foto" oleh skrip -->
                <div id="grid-foto" class="mt-4 grid grid-cols-3 gap-3">
                    <!-- MULAI LOOP: Foto fisik barang dari database (satu iterasi = satu foto). TODO Backend: isi "url" dengan alamat foto tersimpan -->
                    @foreach ($penerimaan['foto'] as $foto)
                        <x-penerimaan.foto :label="$foto['label']" :url="$foto['url']" :foto-id="$foto['id']" :bisa-dipilih="! $selesai" />
                    @endforeach
                    <!-- AKHIR LOOP: Foto fisik barang -->
                </div>

                @if ($selesai)
                    <!-- Kotak ringkasan: jumlah foto tersimpan dan nomor penerimaan terkait -->
                    <div class="mt-4 rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-[10px]">
                        <p class="text-[12px] font-bold leading-4 text-tosca-400">{{ count($penerimaan['foto']) }} foto tersimpan</p>
                        <p class="text-[11px] leading-4 text-gray-500">Dokumentasi terhubung ke {{ $penerimaan['nomor'] }}</p>
                    </div>
                @else
                    <p class="mt-4 text-[12px] leading-5 text-gray-600">Unggah beberapa foto sesuai kebutuhan. Tidak ada batas tetap jumlah foto atau kewajiban enam sisi; ketiga foto yang ada dipertahankan. Klik foto untuk menandainya sebelum menekan Hapus Foto.</p>

                    <!-- Input berkas foto (tersembunyi, banyak berkas); foto dari form ini dikirim lewat foto_barang[] -->
                    <input id="input-foto" name="foto_barang[]" type="file" accept="image/*" multiple class="hidden">
                    <!-- Tempat input tersembunyi hapus_foto[] untuk foto tersimpan yang dihapus (diisi skrip) -->
                    <div id="hapus-foto-tersimpan"></div>

                    <div class="mt-4 flex gap-3">
                        <button type="button" data-pilih-foto class="h-10 rounded-lg bg-tosca-900 px-5 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Tambah Foto</button>
                        <button type="button" id="tombol-hapus-foto" disabled class="h-10 rounded-lg bg-tosca-900 px-5 text-[13px] font-semibold text-white transition hover:bg-tosca-800 disabled:cursor-not-allowed disabled:opacity-50">Hapus Foto</button>
                    </div>
                @endif
            </section>

            <!-- Kartu Lampiran Dokumen Pendukung: daftar berkas + (hanya "dijalankan") unggah sertifikat dan dokumen serah terima -->
            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
                <h2 class="text-[14px] font-bold leading-5">Lampiran Dokumen Pendukung</h2>

                <ul id="daftar-lampiran" class="mt-4 space-y-3">
                    <!-- MULAI LOOP: Lampiran pendukung dari database (satu iterasi = satu berkas). Tombol sampah (hanya "dijalankan") menandai lampiran untuk dihapus -->
                    @foreach ($penerimaan['lampiran'] as $indeks => $berkas)
                        <li data-lampiran class="flex items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-[10px]">
                            <svg class="h-[18px] w-[18px] shrink-0 text-[#26ae60]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikonCentang }}" /></svg>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[12px] font-bold leading-4">{{ $berkas['judul'] }}</p>
                                <p class="truncate text-[11px] leading-4 text-gray-500">{{ $berkas['berkas'] }}</p>
                            </div>
                            @unless ($selesai)
                                <button type="button" data-hapus-lampiran="{{ $indeks }}" aria-label="Hapus {{ $berkas['judul'] }}" class="text-gray-500 transition hover:text-[#e5484d]">
                                    <svg class="h-[16px] w-[16px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikonSampah }}" /></svg>
                                </button>
                            @endunless
                        </li>
                    @endforeach
                    <!-- AKHIR LOOP: Lampiran pendukung -->

                    @unless ($selesai)
                        <!-- Sertifikat Uji Fungsi (Pihak Ketiga): unggahan opsional -->
                        <li class="flex items-center gap-3 rounded-lg border border-dashed border-gray-300 bg-[#fafbfc] px-3 py-[10px]">
                            <svg class="h-[18px] w-[18px] shrink-0 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[12px] font-bold leading-4">Sertifikat Uji Fungsi (Pihak Ketiga)</p>
                                <p id="nama-sertifikat" class="truncate text-[11px] leading-4 text-gray-500">Unggah dokumen jika ada (.pdf)</p>
                            </div>
                            <label for="sertifikat_uji_fungsi" class="cursor-pointer rounded-md border border-gray-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-gray-900 transition hover:bg-gray-50">Pilih File</label>
                            <input id="sertifikat_uji_fungsi" name="sertifikat_uji_fungsi" type="file" accept=".pdf" class="hidden">
                        </li>
                    @endunless
                </ul>

                @unless ($selesai)
                    <!-- Tempat input tersembunyi hapus_lampiran[] untuk lampiran tersimpan yang dihapus (diisi skrip) -->
                    <div id="hapus-lampiran-tersimpan"></div>

                    <!-- Dokumen Serah Terima Penyedia/Vendor (wajib): kolom putus-putus yang membuka pemilih berkas -->
                    <label for="dokumen_serah_terima" class="mb-2 mt-4 block text-[12px] font-medium leading-4 text-gray-700">Dokumen Serah Terima Penyedia/Vendor *</label>
                    <label for="dokumen_serah_terima" id="area-serah-terima" class="flex h-[44px] cursor-pointer items-center rounded-lg border border-dashed border-gray-300 bg-[#fafbfc] px-4 text-[12px] text-gray-500 transition hover:border-tosca-400">
                        <span id="nama-serah-terima" class="truncate">Pilih PDF/foto dokumen serah terima dari penyedia...</span>
                    </label>
                    <input id="dokumen_serah_terima" name="dokumen_serah_terima" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="sr-only">
                @endunless
            </section>
        </div>
    </div>

@unless ($selesai)
    <!-- Cetakan foto baru dan kotak kosong "Tambah Foto" (disalin oleh skrip) -->
    <template id="templat-foto"><x-penerimaan.foto bisa-dipilih /></template>
    <template id="templat-slot">
        <button type="button" data-slot-kosong class="flex h-[90px] flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-gray-300 bg-[#fafbfc] text-[11px] text-gray-500 transition hover:border-tosca-400">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Tambah Foto
        </button>
    </template>

    <!-- Modal Konfirmasi Bukti Penerimaan: di dalam form agar tombol "Konfirmasi" mengirim seluruh isian -->
    <x-penerimaan.modal-konfirmasi :nomor-spk="$penerimaan['nomor_spk']" />
</form>

@push('skrip')
<script>
    (function () {
        const form = document.getElementById('form-penerimaan');
        const grid = document.getElementById('grid-foto');
        const inputFoto = document.getElementById('input-foto');
        const tombolHapusFoto = document.getElementById('tombol-hapus-foto');
        const modal = document.getElementById('modal-penerimaan');
        const kumpulan = new DataTransfer();
        const MIN_KOTAK = 6;

        const semuaFoto = () => [...grid.querySelectorAll('[data-foto]')];
        const kunciBerkas = (f) => f.name + '|' + f.size + '|' + f.lastModified;

        // Jaga jumlah kotak kosong: minimal 6 kotak dan selalu kelipatan 3 agar baris terakhir rapi
        function rapikanKotak() {
            grid.querySelectorAll('[data-slot-kosong]').forEach((el) => el.remove());
            const jumlah = semuaFoto().length;
            const target = Math.max(MIN_KOTAK, Math.ceil(jumlah / 3) * 3);
            for (let i = jumlah; i < target; i++) {
                grid.appendChild(document.getElementById('templat-slot').content.firstElementChild.cloneNode(true));
            }
            tombolHapusFoto.disabled = !grid.querySelector('[data-foto][data-terpilih]');
        }

        // Foto baru: tampil sebagai pratinjau; berkasnya dikumpulkan di input foto_barang[] agar ikut terkirim
        function tambahFoto(daftar) {
            [...daftar].filter((f) => f.type.startsWith('image/')).forEach((f) => {
                if ([...kumpulan.files].some((x) => kunciBerkas(x) === kunciBerkas(f))) return;
                kumpulan.items.add(f);

                const kotak = document.getElementById('templat-foto').content.firstElementChild.cloneNode(true);
                kotak.dataset.kunci = kunciBerkas(f);
                const gambar = kotak.querySelector('[data-gambar]');
                gambar.src = URL.createObjectURL(f);
                gambar.classList.remove('hidden');
                kotak.querySelector('[data-label]').textContent = f.name;
                grid.insertBefore(kotak, grid.querySelector('[data-slot-kosong]'));
            });
            inputFoto.files = kumpulan.files;
            rapikanKotak();
        }

        document.querySelectorAll('[data-pilih-foto]').forEach((el) => el.addEventListener('click', () => inputFoto.click()));
        inputFoto.addEventListener('change', () => tambahFoto(inputFoto.files));

        // Klik kotak kosong membuka pemilih berkas; klik foto menandainya untuk dihapus
        grid.addEventListener('click', (e) => {
            if (e.target.closest('[data-slot-kosong]')) { inputFoto.click(); return; }
            const foto = e.target.closest('[data-foto]');
            if (foto) {
                foto.toggleAttribute('data-terpilih');
                tombolHapusFoto.disabled = !grid.querySelector('[data-foto][data-terpilih]');
            }
        });

        // Hapus foto terpilih: foto tersimpan dikirim lewat hapus_foto[], foto baru dikeluarkan dari kumpulan berkas
        tombolHapusFoto.addEventListener('click', () => {
            grid.querySelectorAll('[data-foto][data-terpilih]').forEach((foto) => {
                if (foto.dataset.fotoId) {
                    const isian = document.createElement('input');
                    isian.type = 'hidden';
                    isian.name = 'hapus_foto[]';
                    isian.value = foto.dataset.fotoId;
                    document.getElementById('hapus-foto-tersimpan').appendChild(isian);
                } else {
                    const sisa = [...kumpulan.files].filter((f) => kunciBerkas(f) !== foto.dataset.kunci);
                    kumpulan.items.clear();
                    sisa.forEach((f) => kumpulan.items.add(f));
                    inputFoto.files = kumpulan.files;
                    URL.revokeObjectURL(foto.querySelector('[data-gambar]').src);
                }
                foto.remove();
            });
            rapikanKotak();
        });

        // Hapus lampiran tersimpan: baris disembunyikan dan indeksnya dikirim lewat hapus_lampiran[]
        document.querySelectorAll('[data-hapus-lampiran]').forEach((tombol) => tombol.addEventListener('click', () => {
            const isian = document.createElement('input');
            isian.type = 'hidden';
            isian.name = 'hapus_lampiran[]';
            isian.value = tombol.dataset.hapusLampiran;
            document.getElementById('hapus-lampiran-tersimpan').appendChild(isian);
            tombol.closest('[data-lampiran]').classList.add('hidden');
        }));

        // Nama berkas yang dipilih ditampilkan pada kolom unggah
        const tampilkanNama = (idInput, idTeks, bawaan) => document.getElementById(idInput).addEventListener('change', (e) => {
            document.getElementById(idTeks).textContent = e.target.files.length ? e.target.files[0].name : bawaan;
        });
        tampilkanNama('sertifikat_uji_fungsi', 'nama-sertifikat', 'Unggah dokumen jika ada (.pdf)');
        tampilkanNama('dokumen_serah_terima', 'nama-serah-terima', 'Pilih PDF/foto dokumen serah terima dari penyedia...');

        // ===== Modal Konfirmasi Bukti Penerimaan =====
        const buka = () => { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };
        const tutup = () => { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); };
        const isi = (kunci, teks) => { modal.querySelector('[data-ringkasan="' + kunci + '"]').textContent = teks || '-'; };

        // "Kirim Bukti Penerimaan": periksa isian wajib dulu, lalu isi ringkasan dan buka modal
        document.getElementById('tombol-kirim').addEventListener('click', () => {
            if (!form.reportValidity()) return;

            const tanggal = form.tanggal_penerimaan.value;
            const jumlahLampiran = document.querySelectorAll('[data-lampiran]:not(.hidden)').length
                + (form.sertifikat_uji_fungsi.files.length ? 1 : 0) + (form.dokumen_serah_terima.files.length ? 1 : 0);

            isi('nama', form.nama_penerima.value.trim());
            isi('unit', form.unit_bagian.value.trim());
            isi('tanggal', tanggal ? new Date(tanggal + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '');
            isi('total_item', semuaFoto().length + ' foto, ' + jumlahLampiran + ' lampiran');
            buka();
        });

        modal.querySelectorAll('[data-tutup-modal]').forEach((el) => el.addEventListener('click', tutup));
        modal.addEventListener('click', (e) => { if (e.target === modal || e.target === modal.firstElementChild) tutup(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) tutup(); });

        // Enter di dalam kolom isian memicu tombol "Konfirmasi" yang tersembunyi: arahkan ke alur modal agar konfirmasi tidak terlewati
        // TODO Backend: setelah action form diisi route penyimpanan, pengiriman dari modal berjalan normal.
        form.addEventListener('submit', (e) => {
            if (modal.classList.contains('hidden')) {
                e.preventDefault();
                document.getElementById('tombol-kirim').click();
            } else if (!form.getAttribute('action')) {
                e.preventDefault();
                tutup();
            }
        });

        rapikanKotak();
    })();
</script>
@endpush
@endunless

@endsection
