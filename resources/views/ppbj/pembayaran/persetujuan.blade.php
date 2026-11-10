@extends('layouts.ppbj')

@section('judul_halaman', 'Persetujuan Paket Pembayaran')
@section('menu_aktif', 'pembayaran')
@section('url_kembali', route('ppbj.pembayaran.index'))
@section('breadcrumb_ekstra', 'Persetujuan Paket Pembayaran')

@section('konten')

{{--
    Persetujuan Paket Pembayaran (PPBJ) - satu view untuk dua keadaan, ditentukan oleh $pembayaran['bisa_terbit']:
      true  (status 'diproses')   : kartu "Terbitkan Perintah Pembayaran?" dengan tombol "Terbitkan Perintah" AKTIF (membuka modal konfirmasi)
      false (status 'pembayaran') : perintah sudah terbit dan menunggu Keuangan; tombol NONAKTIF abu-abu
    Variabel dari Ppbj\PembayaranController@show: $pembayaran - lihat App\Support\Ppbj\DataPpbj::detailPembayaran()
      judul, kode, unit, nilai, vendor, ppk, catatan_paket, dokumen_paket[], perintah[] (pasangan [label, isi]), berkas_perintah,
      id_paket, penerbit, tujuan, total, bisa_terbit

    Status 'selesai' TIDAK memakai view ini: Ppbj\PembayaranController@show merender view Keuangan "Pembayaran Selesai" (keuangan.pembayaran.show)
    dengan layout PPBJ, setara dengan: @include('keuangan.pembayaran.show', ['layoutRole' => 'ppbj', ...]) - HTML-nya tidak ditulis ulang.
--}}

<div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[minmax(0,1fr)_366px]">

    <!-- ---------- KOLOM KIRI: ringkasan paket + Dokumen Paket Pembayaran ---------- -->
    <div class="space-y-6">

        <!-- Kartu ringkasan paket: judul, kode, Unit / Nilai Pengadaan / Vendor / PPK, dan kotak informasi biru -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[18px] font-bold leading-6">{{ $pembayaran['judul'] }}</h2>
            <p class="mt-[3px] text-[11px] leading-4 text-gray-500">{{ $pembayaran['kode'] }}</p>

            <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-[14px] md:grid-cols-2">
                @foreach ([['Unit', $pembayaran['unit']], ['Nilai Pengadaan', $pembayaran['nilai']], ['Vendor', $pembayaran['vendor']], ['PPK', $pembayaran['ppk']]] as [$label, $isi])
                    <div>
                        <dt class="text-[12px] leading-4 text-gray-500">{{ $label }}</dt>
                        <dd class="mt-[5px] text-[14px] leading-5 text-gray-900">{{ $isi }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-5 flex items-center gap-3 rounded-lg bg-[#e8f1fd] px-[18px] py-[14px] text-[13px] leading-5 text-[#3b6fe0]">
                <svg class="h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                {{ $pembayaran['catatan_paket'] }}
            </div>
        </section>

        <!-- Kartu Dokumen Paket Pembayaran: satu baris per dokumen dengan tautan Lihat / Unduh -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Dokumen Paket Pembayaran</h2>

            <ul class="mt-4 space-y-3">
                <!-- MULAI LOOP: Dokumen paket pembayaran dari database (satu iterasi = satu dokumen) -->
                @foreach ($pembayaran['dokumen_paket'] as $dokumen)
                    <x-ppbj.berkas :judul="$dokumen['judul']" :keterangan="$dokumen['keterangan']" />
                @endforeach
                <!-- AKHIR LOOP: Dokumen paket pembayaran -->
            </ul>
        </section>
    </div>

    <!-- ---------- KOLOM KANAN: Perintah Pembayaran + Terbitkan Perintah ---------- -->
    <div class="space-y-6">

        <!-- Kartu Perintah Pembayaran: pasangan label-isi dan berkas perintah -->
        <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Perintah Pembayaran</h2>

            <dl class="mt-3 space-y-[10px]">
                <!-- MULAI LOOP: Baris perintah pembayaran (nomor perintah, ID paket, penerbit, tujuan, jumlah, realisasi, dst.) -->
                @foreach ($pembayaran['perintah'] as [$label, $isi])
                    <div class="grid grid-cols-[120px_1fr] gap-x-3 text-[13px] leading-5">
                        <dt class="text-gray-500">{{ $label }}</dt>
                        <dd class="font-medium text-gray-900">{{ $isi }}</dd>
                    </div>
                @endforeach
                <!-- AKHIR LOOP: Baris perintah pembayaran -->
            </dl>

            <ul class="mt-4">
                <x-ppbj.berkas :judul="$pembayaran['berkas_perintah']['judul']" :keterangan="$pembayaran['berkas_perintah']['keterangan']" />
            </ul>
        </section>

        <!-- Kartu "Terbitkan Perintah Pembayaran?": tombol hanya MEMBUKA modal; pengiriman terjadi lewat form di dalam kartu ini -->
        {{--
            TODO Backend: isi action dengan route penerbitan perintah (mis. route('ppbj.pembayaran.terbitkan', $pembayaran['id'])).
            Field: pembayaran_id. Server wajib memeriksa paket sudah disetujui PPK dan Wadir 2 sebelum mencatat perintah atas nama penerbit
            dan meneruskannya ke Keuangan.
        --}}
        <form method="POST" action="">
            @csrf
            <input type="hidden" name="pembayaran_id" value="{{ $pembayaran['id'] }}">

            <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-6 pt-6 shadow-[0_4px_14px_rgba(15,23,42,0.08)]">
                <h2 class="text-[18px] font-bold leading-6">Terbitkan Perintah Pembayaran?</h2>
                <p class="mt-3 text-[14px] leading-[22px] text-gray-800">
                    Paket {{ $pembayaran['id_paket'] }} senilai {{ $pembayaran['total'] }} telah disetujui PPK dan Wadir 2.
                    Perintah akan dicatat atas nama Mahfudh Al Rafif dan dikirim ke Keuangan.
                </p>

                <div class="mt-4 flex justify-end">
                    @if ($pembayaran['bisa_terbit'])
                        <button type="button" data-buka-modal-ppbj="modal-terbitkan-perintah"
                                class="h-10 rounded-lg bg-tosca-900 px-6 text-[13px] font-semibold text-white transition hover:bg-tosca-800">Terbitkan Perintah</button>
                    @else
                        <button type="button" disabled
                                class="h-10 cursor-not-allowed rounded-lg bg-[#e5e7ea] px-6 text-[13px] font-semibold text-gray-400">Terbitkan Perintah</button>
                    @endif
                </div>
            </section>

            <!-- Modal "Terbitkan Perintah": di dalam form agar tombol Konfirmasi mengirim penerbitan -->
            @if ($pembayaran['bisa_terbit'])
                <x-ppbj.modal-konfirmasi id="modal-terbitkan-perintah" judul="Terbitkan Perintah"
                    pesan="Perintah pembayaran akan dicatat atas nama penerbit dan dikirim ke Keuangan. Tindakan ini tidak dapat dibatalkan."
                    :ringkasan="[
                        ['Nomor Perintah', $pembayaran['nomor_perintah']],
                        ['ID Paket', $pembayaran['id_paket']],
                        ['Penerbit', $pembayaran['penerbit']],
                        ['Tujuan', $pembayaran['tujuan']],
                        ['Total', $pembayaran['total']],
                    ]" />
            @endif
        </form>
    </div>
</div>

<p class="mt-6 text-[11px] text-gray-500">SIMPATIK-PBJ · Tahun Anggaran 2026 · Akses dan tindakan mengikuti kewenangan peran.</p>

@endsection
