@extends('layouts.ppbj')

@section('judul_halaman', 'Unggah Dokumen Pembayaran')
@section('menu_aktif', 'pembayaran')
@section('url_kembali', route('ppbj.pembayaran.index'))
@section('breadcrumb_ekstra', 'Unggah Dokumen Pembayaran')

@section('konten')

{{--
    Unggah Dokumen Pembayaran - satu view untuk dua keadaan, ditentukan oleh $pembayaran['status']:
      'tinjauan'    : zona unggah + daftar dokumen + tombol "Kirim Untuk Persetujuan" AKTIF (membuka modal konfirmasi)
      'persetujuan' : hanya daftar dokumen yang sudah terkirim; tombol "Kirim untuk Persetujuan" NONAKTIF
    Variabel dari Ppbj\PembayaranController@show: $pembayaran - lihat App\Support\Ppbj\DataPpbj::detailPembayaran()
      judul, kode, unit, nilai, ppk, kontrak_id, kontrak, penerimaan, catatan_terima, dokumen_unggah[] (judul, keterangan)

    Tombol "Lihat Dokumentasi Penerimaan" membuka Dokumentasi Penerimaan versi hanya-baca (view penerimaan.dokumentasi.show dengan $bacaSaja = true)
    lewat route ppbj.kontrak.penerimaan; HTML-nya tidak ditulis ulang di sini.
--}}

@php $bisaKirim = $pembayaran['status'] === 'tinjauan'; @endphp

<div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">

    <!-- ---------- KOLOM KIRI: ringkasan pengadaan + Dasar Pembayaran ---------- -->
    <div class="space-y-6">
        <x-ppbj.kartu-pengadaan :data="$pembayaran" label-tombol="Lihat Dokumentasi Penerimaan" :url="route('ppbj.kontrak.penerimaan', $pembayaran['kontrak_id'])" />

        <!-- Kartu Dasar Pembayaran: nomor kontrak, ringkasan penerimaan barang, dan siapa yang mencatat -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Dasar Pembayaran</h2>

            <dl class="mt-4 grid grid-cols-2 gap-x-6">
                <div>
                    <dt class="text-[12px] leading-4 text-gray-500">Kontrak</dt>
                    <dd class="mt-[5px] text-[14px] leading-5">{{ $pembayaran['kontrak'] }}</dd>
                </div>
                <div>
                    <dt class="text-[12px] leading-4 text-gray-500">Penerimaan</dt>
                    <dd class="mt-[5px] text-[14px] leading-5">{{ $pembayaran['penerimaan'] }}</dd>
                </div>
            </dl>
            <p class="mt-4 text-[12px] leading-4 text-gray-500">{{ $pembayaran['catatan_terima'] }}</p>
        </section>
    </div>

    <!-- ---------- KOLOM KANAN: Dokumen Pembayaran ---------- -->
    {{--
        TODO Backend: isi action dengan route pengiriman dokumen pembayaran untuk persetujuan (mis. route('ppbj.pembayaran.kirim', $pembayaran['id'])).
        Form multipart membungkus zona unggah dan modal "Kirim Untuk Persetujuan". Field: pembayaran_id, dokumen_pembayaran (file pdf/xlsx/jpg maks 10 MB).
    --}}
    <form method="POST" action="" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="pembayaran_id" value="{{ $pembayaran['id'] }}">

        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Dokumen Pembayaran</h2>

            <!-- Zona unggah hanya pada status "tinjauan" -->
            @if ($bisaKirim)
                <div class="mt-4">
                    <x-ppbj.zona-unggah name="dokumen_pembayaran" judul="Unggah dokumen pendukung proses" />
                </div>
            @endif

            <ul class="mt-4 space-y-3">
                <!-- MULAI LOOP: Dokumen pembayaran dari database (satu iterasi = satu dokumen) -->
                @foreach ($pembayaran['dokumen_unggah'] as $dokumen)
                    <x-ppbj.berkas :judul="$dokumen['judul']" :keterangan="$dokumen['keterangan']" />
                @endforeach
                <!-- AKHIR LOOP: Dokumen pembayaran -->
            </ul>

            <!-- Tombol kirim: aktif membuka modal (tinjauan); nonaktif abu-abu setelah dokumen terkirim (persetujuan) -->
            <div class="mt-4 flex justify-end">
                @if ($bisaKirim)
                    <button type="button" data-buka-modal-ppbj="modal-kirim-persetujuan"
                            class="h-10 rounded-lg bg-tosca-900 px-6 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Kirim Untuk Persetujuan</button>
                @else
                    <button type="button" disabled
                            class="h-10 cursor-not-allowed rounded-lg bg-[#e5e7ea] px-6 text-[13px] font-semibold text-gray-400">Kirim untuk Persetujuan</button>
                @endif
            </div>
        </section>

        <!-- Modal "Kirim Untuk Persetujuan": di dalam form agar tombol Konfirmasi mengirim dokumen -->
        @if ($bisaKirim)
            <x-ppbj.modal-konfirmasi id="modal-kirim-persetujuan" judul="Kirim Untuk Persetujuan"
                pesan="Dokumen pembayaran akan dikirim ke PPK dan Wadir 2 untuk persetujuan. Setelah dikirim, dokumen tidak dapat diubah."
                :ringkasan="[
                    ['Nomor Pengadaan', 'PBJ-2026-0145'],
                    ['Nomor Kontrak', $pembayaran['kontrak']],
                    ['Nilai Pengadaan', $pembayaran['nilai']],
                    ['Jumlah Dokumen', count($pembayaran['dokumen_unggah']).' dokumen'],
                ]" />
        @endif
    </form>
</div>

<p class="mt-6 text-[11px] text-gray-500">SIMPATIK-PBJ · Tahun Anggaran 2026 · Akses dan tindakan mengikuti kewenangan peran.</p>

@endsection
