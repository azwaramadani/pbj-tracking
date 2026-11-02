@extends('layouts.wadir2')

@section('judul_halaman', 'Persetujuan Paket Pembayaran')
@section('menu_aktif', 'pembayaran')
@section('url_kembali', route('wadir2.pembayaran.index'))
@section('breadcrumb_ekstra', 'Persetujuan Paket Pembayaran')
@section('pil_ta_abu', '1')

@section('konten')

{{--
    Variabel dari Wadir2\PembayaranController@show:
      $paket : id, judul, kode, unit, nilai, vendor, ppk, catatan,
               informasi[] (pasangan [label, isi]), dokumen[] (judul, berkas, tipe, ukuran, tanggal),
               wadir2{judul,subjudul,status,penanggung_jawab,waktu}, perencanaan{...sama...}, bisa_memutuskan (bool)
               - lihat App\Support\Wadir2\DataPembayaran::cari()
--}}

<!-- ===================== GRID SPLIT: KIRI (paket + dokumen) | KANAN (informasi paket + status) ===================== -->
<div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[minmax(0,1fr)_366px]">

    <!-- ---------- KOLOM KIRI ---------- -->
    <div class="space-y-6">

        <!-- Kartu ringkasan paket: judul, kode, grid 2 kolom (Unit / Nilai Pengadaan / Vendor / PPK), dan kotak informasi biru -->
        <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[18px] font-bold leading-6">{{ $paket['judul'] }}</h2>
            <p class="mt-[3px] text-[11px] leading-4 text-gray-500">{{ $paket['kode'] }}</p>

            <dl class="mt-[15px] grid grid-cols-1 gap-x-6 gap-y-[14px] md:grid-cols-2">
                <div>
                    <dt class="text-[12px] leading-4 text-gray-500">Unit</dt>
                    <dd class="mt-[7px] text-[14px] leading-5 text-gray-900">{{ $paket['unit'] }}</dd>
                </div>
                <div>
                    <dt class="text-[12px] leading-4 text-gray-500">Nilai Pengadaan</dt>
                    <dd class="mt-[7px] text-[14px] leading-5 text-gray-900">{{ $paket['nilai'] }}</dd>
                </div>
                <div>
                    <dt class="text-[12px] leading-4 text-gray-500">Vendor</dt>
                    <dd class="mt-[7px] text-[14px] leading-5 text-gray-900">{{ $paket['vendor'] }}</dd>
                </div>
                <div>
                    <dt class="text-[12px] leading-4 text-gray-500">PPK</dt>
                    <dd class="mt-[7px] text-[14px] leading-5 text-gray-900">{{ $paket['ppk'] }}</dd>
                </div>
            </dl>

            <!-- Kotak informasi biru: aturan alur persetujuan -->
            <div class="mt-5 flex items-center gap-3 rounded-lg bg-[#e8f1fd] px-[18px] py-[14px] text-[13px] leading-5 text-[#3b6fe0]">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
                {{ $paket['catatan'] }}
            </div>
        </section>

        <!-- Kartu Dokumen Paket Pembayaran: satu kartu per berkas dengan tautan "Lihat / Unduh" di kanan -->
        <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Dokumen Paket Pembayaran</h2>

            <ul class="mt-[17px] space-y-[11px]">
                <!-- MULAI LOOP: Dokumen paket pembayaran dari database (satu iterasi = satu berkas: Invoice, Kontrak / SPK, Dokumen Pendukung) -->
                @foreach ($paket['dokumen'] as $dokumen)
                    <li class="flex h-[61px] items-center gap-3 rounded-lg border border-gray-200 bg-[#f6f9fa] px-[15px]">
                        <!-- Ikon dokumen bercentang -->
                        <svg class="h-[18px] w-[18px] shrink-0 text-brand-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zM9.5 15l1.8 1.8 3.4-3.6" />
                        </svg>
                        <div class="min-w-0 flex-1">
                            <!-- Jenis dokumen (tebal) lalu nama berkas · tipe · ukuran · tanggal (kecil, abu-abu) -->
                            <p class="text-[13px] font-bold leading-4">{{ $dokumen['judul'] }}</p>
                            <p class="mt-[3px] truncate text-[11.5px] leading-4 text-gray-500">{{ $dokumen['berkas'] }} · {{ $dokumen['tipe'] }} · {{ $dokumen['ukuran'] }} · {{ $dokumen['tanggal'] }}</p>
                        </div>
                        {{-- TODO Backend: arahkan href ke route lihat/unduh berkas (mis. route('wadir2.pembayaran.dokumen', $dokumen->id)) --}}
                        <a href="#" download class="shrink-0 text-[12px] font-bold text-tosca-400 transition hover:underline">Lihat / Unduh</a>
                    </li>
                @endforeach
                <!-- AKHIR LOOP: Dokumen paket pembayaran -->
            </ul>
        </section>
    </div>

    <!-- ---------- KOLOM KANAN ---------- -->
    <div class="space-y-[24px]">

        <!-- Kartu Informasi Paket: tabel label (abu-abu) dan nilai (tebal) -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Informasi Paket</h2>

            <dl class="mt-[12px] space-y-[9px]">
                <!-- MULAI LOOP: Baris informasi paket (ID Paket, Kontrak, Persetujuan PPK, Catatan PPK, Waktu Review, Status Anda) -->
                @foreach ($paket['informasi'] as [$label, $isi])
                    <div class="grid grid-cols-[116px_1fr] gap-x-3">
                        <dt class="text-[13px] leading-5 text-gray-500">{{ $label }}</dt>
                        <dd class="text-[13.5px] font-medium leading-5 text-gray-900">{{ $isi }}</dd>
                    </div>
                @endforeach
                <!-- AKHIR LOOP: Baris informasi paket -->
            </dl>
        </section>

        {{--
            Dua kartu status persetujuan. URUTAN TETAP: Wadir 2 dahulu, baru Perencanaan.
            Backend hanya mengisi status (menunggu | disetujui | ditolak | revisi), penanggung jawab, dan waktu;
            warna kartu dan teks lencana ditentukan otomatis oleh komponen x-unit.kartu-persetujuan.
        --}}
        <!-- Kartu status Wadir 2 -->
        <x-unit.kartu-persetujuan
            :judul="$paket['wadir2']['judul']"
            :subjudul="$paket['wadir2']['subjudul']"
            :status="$paket['wadir2']['status']"
            :penanggung-jawab="$paket['wadir2']['penanggung_jawab']"
            :waktu="$paket['wadir2']['waktu']"
            kelas-kolom="grid-cols-[133px_1fr]" />

        <!-- Kartu status Perencanaan -->
        <x-unit.kartu-persetujuan
            :judul="$paket['perencanaan']['judul']"
            :subjudul="$paket['perencanaan']['subjudul']"
            :status="$paket['perencanaan']['status']"
            :penanggung-jawab="$paket['perencanaan']['penanggung_jawab']"
            :waktu="$paket['perencanaan']['waktu']"
            kelas-kolom="grid-cols-[133px_1fr]" />

        <!--
            Area aksi: tombol "Tentukan Keputusan" hanya tampil selama persetujuan belum lengkap ($paket['bisa_memutuskan']).
            Tombol hanya MEMBUKA modal; pengiriman keputusan terjadi lewat form POST di dalam modal Konfirmasi Persetujuan di bawah.
        -->
        @if ($paket['bisa_memutuskan'])
            <button type="button" data-buka-modal="modal-konfirmasi"
                    class="h-10 w-full rounded-lg bg-tosca-900 text-[14px] font-semibold text-white transition hover:bg-tosca-800">Tentukan Keputusan</button>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Persetujuan (komponen bersama dengan halaman Detail Permohonan; disembunyikan secara default) -->
@if ($paket['bisa_memutuskan'])
    <x-wadir2.konfirmasi-persetujuan :record-id="$paket['id']" field-name="paket_id" />
@endif

@endsection
