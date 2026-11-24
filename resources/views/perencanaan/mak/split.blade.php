@extends('layouts.perencanaan')

@section('judul_halaman', 'Split MAK')
@section('menu_aktif', 'dashboard')
@section('url_kembali', route('perencanaan.permohonan.show', $split['id']))

@section('konten')

{{--
    Split MAK - membagi nilai permohonan ke beberapa Mata Anggaran Kegiatan (dokumen Induk -> Turunan).
    Variabel dari Perencanaan\SplitMakController@show:
      $split : id, status, kode_induk, total (angka rupiah), opsi_mak[] (kode, label, kategori), baris[] (kode, nominal)
               - lihat App\Support\Perencanaan\DataPerencanaan::split()
    Baris pembagian, bar validasi, dan diagram dikelola skrip di bawah halaman; total yang dikirim WAJIB divalidasi ulang di server.
--}}

@php
    $rupiah = fn (int $angka) => 'Rp '.number_format($angka, 0, ',', '.');

    /* Peta status permohonan asli => [label, kelas warna pil] */
    $pilStatus = [
        'ditolak'  => ['DITOLAK',  'bg-[#fef2f2] text-[#e5322d]'],
        'diproses' => ['DIPROSES', 'bg-[#fef3c7] text-[#f0a30a]'],
        'selesai'  => ['SELESAI',  'bg-[#d9f5e5] text-[#1fa35c]'],
    ];
    [$labelStatus, $kelasStatus] = $pilStatus[$split['status']] ?? $pilStatus['diproses'];
@endphp

{{--
    TODO Backend: isi action dengan route penyimpanan pembagian (mis. route('perencanaan.split-mak.store', $split['id'])).
    Field: mak_alokasi[] (kode MAK) dan nominal[] (angka dengan titik ribuan). Validasi di server: minimal 1 baris,
    nominal > 0, dan jumlah seluruh nominal HARUS sama dengan nilai asli permohonan. Server membuat dokumen turunan (-A, -B, dst.).
--}}
<form id="form-split" method="POST" action="" data-total="{{ $split['total'] }}" data-kode-induk="{{ $split['kode_induk'] }}" class="space-y-6">
    @csrf
    <input type="hidden" name="permohonan_id" value="{{ $split['id'] }}">

    <!-- ===================== KARTU ATAS: TOTAL NILAI PERMOHONAN ASLI + STATUS + TOMBOL PECAH DANA ===================== -->
    <section class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-gray-100 bg-white px-6 py-5 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <div>
            <p class="text-[12px] font-medium uppercase leading-4 text-gray-500">Total Nilai Permohonan Asli</p>
            <p class="mt-1 text-[22px] font-bold leading-7">{{ $rupiah($split['total']) }}</p>
        </div>

        <!-- Pil status permohonan asli -->
        <span class="inline-flex h-[25px] w-[88px] items-center justify-center rounded-md text-[12px] font-bold {{ $kelasStatus }}">{{ $labelStatus }}</span>

        <!-- Tombol pecah dana: menggulir ke bagian distribusi dan memfokuskan pilihan MAK pertama -->
        <button type="button" id="tombol-pecah"
                class="rounded-md bg-[#e8f1fd] px-[14px] py-[7px] text-[12px] font-semibold text-[#3b6fe0] transition hover:bg-[#d9e8fb]">Pecah menjadi beberapa sumber dana</button>
    </section>

    <!-- ===================== KARTU DISTRIBUSI ALOKASI DANA MAK ===================== -->
    <section id="distribusi" class="rounded-2xl border border-gray-100 bg-white px-6 py-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-[16px] font-bold leading-6">Distribusi Alokasi Dana MAK</h2>
            <button type="button" id="tombol-tambah"
                    class="flex h-[30px] items-center gap-1.5 rounded-md border border-tosca-400 bg-white px-3 text-[12px] font-semibold text-tosca-600 transition hover:bg-[#e8f7f5]">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Baris Pembagian
            </button>
        </div>

        <!-- Daftar baris pembagian: baris baru dibuat dengan menyalin baris pertama -->
       
<div id="baris-split" class="mt-4 space-y-3">
    @php
        $barisTampil = count($split['baris']) > 0
            ? $split['baris']
            : [['kode' => '', 'nominal' => 0]];
    @endphp

    @foreach ($barisTampil as $i => $baris)
        <div data-baris class="grid grid-cols-1 gap-x-4 gap-y-2 md:grid-cols-[minmax(0,1fr)_422px]">
            <div>
                <label data-label-mak class="mb-[6px] block text-[12px] font-semibold text-gray-800">
                    MAK Alokasi {{ $i + 1 }}
                </label>

                <div class="relative">
                    <select name="mak_alokasi[]" required
                        class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-white pl-3 pr-9 text-[13px] text-gray-900 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20">
                        <option value="" data-kategori="Belum dipilih">Pilih MAK</option>

                        @foreach ($split['opsi_mak'] as $opsi)
                            <option value="{{ $opsi['kode'] }}"
                                data-kategori="{{ $opsi['kategori'] }}"
                                @selected($opsi['kode'] === $baris['kode'])>
                                {{ $opsi['label'] }}
                            </option>
                        @endforeach
                    </select>

                    <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-600"
                        fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </div>

            <div class="flex items-end gap-3">
                <div class="min-w-0 flex-1">
                    <label class="mb-[6px] block text-[12px] font-semibold text-gray-800">
                        Jumlah Nominal
                    </label>

                    <div class="flex h-10 overflow-hidden rounded-lg border border-gray-200">
                        <span class="flex w-[42px] shrink-0 items-center justify-center bg-[#e9ecef] text-[13px] font-semibold text-gray-700">
                            Rp
                        </span>

                        <input name="nominal[]" type="text" inputmode="numeric"
                            required
                            value="{{ $baris['nominal'] > 0 ? number_format($baris['nominal'], 0, ',', '.') : '' }}"
                            class="min-w-0 flex-1 bg-white px-3 text-[13px] text-gray-900 outline-none">
                    </div>
                </div>

                <button type="button" data-hapus-baris
                    aria-label="Hapus baris pembagian"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-[#f1a9a6] bg-white text-[#e5484d]">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        </div>
    @endforeach

    @if (count($split['opsi_mak']) === 0)
        <p class="mt-3 text-xs text-amber-700">
            Master data MAK belum tersedia. Pilihan MAK akan muncul setelah data MAK ditambahkan.
        </p>
    @endif
</div>

<template id="templat-baris-split">
    <div data-baris class="grid grid-cols-1 gap-x-4 gap-y-2 md:grid-cols-[minmax(0,1fr)_422px]">
        <div>
            <label data-label-mak class="mb-[6px] block text-[12px] font-semibold text-gray-800">
                MAK Alokasi
            </label>

            <div class="relative">
                <select name="mak_alokasi[]" required
                    class="h-10 w-full appearance-none rounded-lg border border-gray-200 bg-white pl-3 pr-9 text-[13px] text-gray-900 outline-none transition focus:border-tosca-600 focus:ring-2 focus:ring-tosca-600/20">
                    <option value="" data-kategori="Belum dipilih">Pilih MAK</option>

                    @foreach ($split['opsi_mak'] as $opsi)
                        <option value="{{ $opsi['kode'] }}"
                            data-kategori="{{ $opsi['kategori'] }}">
                            {{ $opsi['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-end gap-3">
            <div class="min-w-0 flex-1">
                <label class="mb-[6px] block text-[12px] font-semibold text-gray-800">
                    Jumlah Nominal
                </label>

                <div class="flex h-10 overflow-hidden rounded-lg border border-gray-200">
                    <span class="flex w-[42px] shrink-0 items-center justify-center bg-[#e9ecef] text-[13px] font-semibold text-gray-700">
                        Rp
                    </span>

                    <input name="nominal[]" type="text" inputmode="numeric"
                        required
                        class="min-w-0 flex-1 bg-white px-3 text-[13px] text-gray-900 outline-none">
                </div>
            </div>

            <button type="button" data-hapus-baris
                aria-label="Hapus baris pembagian"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-[#f1a9a6] bg-white text-[#e5484d]">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    </div>
</template>


        <!-- Bar validasi: hijau jika jumlah seluruh baris sama dengan nilai asli permohonan, merah jika berbeda -->
        <div class="mt-5 border-t border-gray-200 pt-5">
            <div id="bar-validasi" class="flex flex-wrap items-center justify-between gap-2 rounded-lg border px-4 py-[13px] text-[13px] font-semibold" role="status">
                <span class="flex items-center gap-2">
                    <svg class="h-[17px] w-[17px] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span id="pesan-validasi">Total pembagian sesuai dengan nilai asli permohonan</span>
                </span>
                <span id="total-validasi"></span>
            </div>
        </div>
    </section>

    <!-- ===================== KARTU DIAGRAM HUBUNGAN DOKUMEN (INDUK - TURUNAN) ===================== -->
    <section class="rounded-2xl border border-gray-100 bg-white px-6 py-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <h2 class="text-[16px] font-bold leading-6">Diagram Hubungan Dokumen (Induk-Turunan)</h2>

        <div class="mt-5 flex flex-wrap items-center gap-0 overflow-x-auto">
            <!-- Kotak dokumen induk -->
            <div class="w-[214px] shrink-0 rounded-lg bg-tosca-900 px-4 py-[14px] text-white">
                <p class="text-[11px] uppercase leading-4 text-tosca-400">Induk</p>
                <p class="mt-[2px] text-[14px] font-bold leading-5">{{ $split['kode_induk'] }}</p>
                <p class="mt-[2px] text-[11px] leading-4 text-white/80">Total: {{ $rupiah($split['total']) }}</p>
            </div>

            <!-- Garis penghubung induk ke turunan -->
            <span class="mx-[13px] h-px w-[34px] shrink-0 bg-gray-500" aria-hidden="true"></span>

            <!-- Daftar dokumen turunan: diisi ulang otomatis oleh skrip setiap baris pembagian berubah -->
            <div id="diagram-turunan" class="w-[280px] shrink-0 space-y-[10px]"></div>
        </div>

        <!-- Cetakan satu kotak dokumen turunan (disalin oleh skrip): garis hijau di kiri, judul tebal, nilai kecil -->
        <template id="templat-turunan">
            <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-[#f8fafc] px-3 py-[9px]">
                <span class="h-5 w-[5px] shrink-0 rounded-full bg-tosca-400"></span>
                <div class="min-w-0">
                    <p data-judul class="truncate text-[12px] font-bold leading-4"></p>
                    <p data-nilai class="text-[11px] leading-4 text-gray-500"></p>
                </div>
            </div>
        </template>
    </section>

    <!-- ===================== TOMBOL FOOTER: BATALKAN + SIMPAN ===================== -->
    <div class="flex justify-end gap-3">
        <a href="{{ route('perencanaan.permohonan.show', $split['id']) }}"
           class="flex h-[42px] items-center rounded-lg border border-[#cfe3e8] bg-[#f3f9fb] px-6 text-[13px] font-semibold text-tosca-900 transition hover:bg-[#e8f3f5]">Batalkan</a>
        <button type="submit" id="tombol-simpan"
                class="h-[42px] rounded-lg bg-tosca-900 px-6 text-[13px] font-semibold text-white transition hover:bg-tosca-800 disabled:cursor-not-allowed disabled:opacity-50">Simpan Pembagian Anggaran</button>
    </div>
</form>

@endsection

@push('skrip')
<script>
    (function () {
        const form = document.getElementById('form-split');
        const wadah = document.getElementById('baris-split');
        const diagram = document.getElementById('diagram-turunan');
        const cetakan = document.getElementById('templat-turunan');
        const bar = document.getElementById('bar-validasi');
        const pesan = document.getElementById('pesan-validasi');
        const totalTeks = document.getElementById('total-validasi');
        const simpan = document.getElementById('tombol-simpan');
        const totalAsli = Number(form.dataset.total);
        const kodeInduk = form.dataset.kodeInduk;

        const kelasSesuai = ['border-[#b9e8cc]', 'bg-[#e8f8ee]', 'text-[#1fa35c]'];
        const kelasSelisih = ['border-[#f5c2c2]', 'bg-[#fef2f2]', 'text-[#e5484d]'];

        const rupiah = (n) => 'Rp ' + n.toLocaleString('id-ID');
        const angka = (el) => Number(el.value.replace(/\D/g, '')) || 0;
        const semuaBaris = () => [...wadah.querySelectorAll('[data-baris]')];

        // Hitung ulang nomor baris, total, bar validasi, tombol simpan, dan diagram turunan
        
function segarkan() {
    const baris = semuaBaris();
    let jumlah = 0;
    let adaKosong = baris.length === 0;

    diagram.innerHTML = '';

    baris.forEach((b, i) => {
        b.querySelector('[data-label-mak]').textContent =
            'MAK Alokasi ' + (i + 1);

        b.querySelector('[data-hapus-baris]').disabled =
            baris.length === 1;

        const nominal = angka(b.querySelector('[name="nominal[]"]'));
        const pilihanMak = b.querySelector('select').value;

        jumlah += nominal;

        if (nominal <= 0 || !pilihanMak) {
            adaKosong = true;
        }

        const opsiTerpilih =
            b.querySelector('select').selectedOptions[0];

        const kategori =
            opsiTerpilih?.dataset.kategori || 'Belum dipilih';

        const kotak =
            cetakan.content.firstElementChild.cloneNode(true);

        kotak.querySelector('[data-judul]').textContent =
            kodeInduk + '-' + String.fromCharCode(65 + i) +
            ' (' + kategori + ')';

        kotak.querySelector('[data-nilai]').textContent =
            rupiah(nominal);

        diagram.appendChild(kotak);
    });

    const sesuai =
        jumlah === totalAsli &&
        baris.length > 0 &&
        !adaKosong;

    bar.classList.remove(...kelasSesuai, ...kelasSelisih);
    bar.classList.add(...(sesuai ? kelasSesuai : kelasSelisih));

    pesan.textContent = sesuai
        ? 'Total pembagian sesuai dengan nilai asli permohonan'
        : 'Total pembagian belum sesuai dengan nilai asli permohonan';

    totalTeks.textContent =
        rupiah(jumlah) + ' / ' + rupiah(totalAsli);

    simpan.disabled = !sesuai;
}
    segarkan();

    })();
</script>
@endpush
    
