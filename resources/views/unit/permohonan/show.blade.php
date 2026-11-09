@extends('layouts.unit')

@section('judul_halaman', 'Detail Permohonan')
@section('menu_aktif', 'permohonan')
@section('url_kembali', url()->previous(route('unit.dashboard')))

@section('konten')

{{--
    Variabel dari Unit\PermohonanController@show:
      $permohonan : satu permohonan (id, no_surat, judul, nilai_anggaran, unit, deskripsi, tgl_kirim_lengkap, lampiran[],
                    persetujuan[wadir2|perencanaan], riwayat[]) - lihat App\Support\Unit\DataPermohonan
      $alur       : daftar langkah "Alur Perjalanan Pengadaan" untuk modal Lacak Pengajuan
--}}

@php
    /* Baris "Informasi Surat Permohonan": [label, isi]. TODO Backend: isi dari kolom tabel permohonan */
    $infoSurat = [
        ['Nomor Surat',          $permohonan['no_surat']],
        ['Judul Pengadaan',      $permohonan['judul']],
        ['Nilai Anggaran',       $permohonan['nilai_anggaran']],
        ['Asal Unit Pengusul',   $permohonan['unit']],
        ['Deskripsi & Urgensi',  $permohonan['deskripsi']],
        ['Tanggal Pengajuan',    $permohonan['tgl_kirim_lengkap']],
    ];
@endphp

<!-- Komponen bersama x-unit.ringkasan-permohonan: Informasi Surat Permohonan (kiri) + Lampiran Dokumen dan kartu status Wadir 2 lalu Perencanaan (kanan) -->
<x-unit.ringkasan-permohonan
    :baris="$infoSurat"
    :lampiran="$permohonan['lampiran']"
    :kartu="[$permohonan['persetujuan']['wadir2'], $permohonan['persetujuan']['perencanaan']]" />

<!-- ===================== KARTU RIWAYAT AKTIVITAS ===================== -->
<section class="mt-[26px] rounded-2xl border border-gray-100 bg-white p-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">

    <!-- Judul kartu + tahun di sisi kanan. TODO Backend: tahun dapat dijadikan filter riwayat -->
    <div class="flex items-center justify-between">
        <h2 class="text-[17px] font-bold leading-6">Riwayat Aktivitas</h2>
        <span class="pr-[1px] text-[12px] text-gray-500">2026</span>
    </div>

    <!-- Tabel riwayat (bisa di-scroll horizontal pada layar sempit) -->
    <div class="mt-[14px] overflow-x-auto">
        <table class="w-full min-w-[900px] table-fixed overflow-hidden rounded-lg border-separate border-spacing-0 border border-gray-200 text-left text-[13px]">
            <colgroup>
                <col class="w-[136px]">
                <col class="w-[155px]">
                <col class="w-[102px]">
                <col class="w-[175px]">
                <col class="w-[140px]">
                <col class="w-[117px]">
                <col>
            </colgroup>

            <!-- Kepala tabel: latar abu-abu muda -->
            <thead class="bg-[#f3f4f6] text-[12px] font-semibold text-gray-800">
                <tr class="h-[38px]">
                    <th class="px-3 font-semibold">Tanggal &amp; Waktu</th>
                    <th class="px-3 font-semibold">User</th>
                    <th class="px-3 font-semibold">Role</th>
                    <th class="px-3 font-semibold">Aktivitas</th>
                    <th class="px-3 font-semibold">Status Sebelumnya</th>
                    <th class="px-3 font-semibold">Status Baru</th>
                    <th class="px-3 font-semibold">Catatan</th>
                </tr>
            </thead>

            <tbody>
                <!-- MULAI LOOP: Baris riwayat aktivitas dari database (satu iterasi = satu baris log perubahan status) -->
                @foreach ($permohonan['riwayat'] as $log)
                    <tr class="h-[56px]">
                        <td class="border-t border-gray-200 px-3 leading-[17px]">{{ $log['waktu'] }}</td>
                        <td class="border-t border-gray-200 px-3 leading-[17px]">{{ $log['user'] }}</td>
                        <td class="border-t border-gray-200 px-3 leading-[17px] text-gray-400">{{ $log['role'] }}</td>
                        <td class="border-t border-gray-200 px-3 leading-[17px]">{{ $log['aktivitas'] }}</td>
                        <td class="border-t border-gray-200 px-3 leading-[17px] text-gray-400">{{ $log['sebelumnya'] }}</td>
                        <td class="border-t border-gray-200 px-3 leading-[17px]">{{ $log['baru'] }}</td>
                        <td class="border-t border-gray-200 px-3 leading-[17px] text-gray-400">{{ $log['catatan'] }}</td>
                    </tr>
                @endforeach
                <!-- AKHIR LOOP: Baris riwayat aktivitas -->
            </tbody>
        </table>
    </div>
</section>

<!-- ===================== TOMBOL AKSI DI BAWAH HALAMAN ===================== -->
<div class="mt-6 flex flex-wrap gap-[17px]">
    <!-- "Lacak Pengajuan" membuka modal Alur Perjalanan Pengadaan (diatur oleh JavaScript di bagian bawah halaman) -->
    <button type="button" data-buka-modal="modal-tracking"
            class="h-10 rounded-lg bg-tosca-900 px-5 text-[14px] font-semibold text-white transition hover:bg-tosca-800">Lacak Pengajuan</button>

    <!-- "Revisi Pengajuan" menuju formulir revisi. TODO Backend: tampilkan hanya bila status mengizinkan revisi -->
    <a href="{{ Route::has('unit.permohonan.revisi') ? route('unit.permohonan.revisi', $permohonan['id']) : '#' }}"
       class="flex h-10 items-center rounded-lg border border-gray-200 bg-white px-5 text-[14px] font-semibold text-tosca-900 transition hover:bg-gray-50">Revisi Pengajuan</a>
</div>

<!-- ===================== MODAL ALUR PERJALANAN PENGADAAN (disembunyikan secara default) ===================== -->
<div id="modal-tracking" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/20" role="dialog" aria-modal="true" aria-labelledby="judul-modal-tracking">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="w-full max-w-[665px] rounded-2xl border border-gray-200 bg-white px-[23px] pb-[14px] pt-[21px] shadow-xl">

            <h2 id="judul-modal-tracking" class="text-center text-[18px] font-bold leading-6">Alur Perjalanan Pengadaan</h2>

            {{--
                Timeline vertikal. Setiap langkah punya state dari backend:
                  selesai  = lingkaran hijau + centang
                  aktif    = lingkaran biru + titik putih, judul berwarna biru
                  menunggu = lingkaran abu-abu, keterangan "Yang akan datang"
                  ditolak  = lingkaran merah + tanda silang
                Garis penghubung vertikal digambar oleh pseudo-element pada setiap item kecuali yang terakhir.
            --}}
            <ol class="mt-[27px]">
                <!-- MULAI LOOP: Langkah alur pengadaan dari database (satu iterasi = satu langkah timeline) -->
                @foreach ($alur as $langkah)
                    @php
                        $kelasIkon = [
                            'selesai'  => 'bg-[#22b35e]',
                            'aktif'    => 'bg-[#2f7bf0]',
                            'menunggu' => 'bg-[#e5e7eb]',
                            'ditolak'  => 'bg-[#e5484d]',
                        ][$langkah['state']];
                        $kelasJudul = ['aktif' => 'text-[#2f7bf0]', 'ditolak' => 'text-[#e5484d]'][$langkah['state']] ?? 'text-gray-900';
                    @endphp

                    <li class="relative flex gap-[20px] pb-[17px] after:absolute after:left-[8px] after:top-[23px] after:-bottom-px after:w-px after:bg-gray-300 last:pb-0 last:after:hidden">

                        <!-- Penanda status langkah -->
                        <span class="relative z-10 mt-[1px] flex h-[18px] w-[18px] shrink-0 items-center justify-center rounded-full {{ $kelasIkon }}">
                            @if ($langkah['state'] === 'selesai')
                                <svg class="h-[10px] w-[10px]" fill="none" stroke="white" stroke-width="3.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12.5l4.5 4.5L19 7.5" /></svg>
                            @elseif ($langkah['state'] === 'aktif')
                                <span class="h-[6px] w-[6px] rounded-full bg-white"></span>
                            @elseif ($langkah['state'] === 'ditolak')
                                <svg class="h-[9px] w-[9px]" fill="none" stroke="white" stroke-width="3.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
                            @endif
                        </span>

                        <!-- Teks langkah: nomor + judul (tebal), peran penanggung jawab (kecil), lalu keterangan -->
                        <div class="min-w-0">
                            <p class="text-[14px] font-bold leading-5 {{ $kelasJudul }}">
                                {{ $langkah['nomor'] }} {{ $langkah['judul'] }}
                                @if ($langkah['peran'])
                                    <span class="ml-1.5 align-[1px] text-[11px] font-semibold text-gray-600">{{ $langkah['bullet'] ? '• ' : '' }}{{ $langkah['peran'] }}</span>
                                @endif
                            </p>
                            <p class="mt-[1px] text-[12px] leading-[18px] text-gray-500">{{ $langkah['keterangan'] }}</p>
                        </div>
                    </li>
                @endforeach
                <!-- AKHIR LOOP: Langkah alur pengadaan -->
            </ol>

            <!-- Tombol penutup modal -->
            <div class="mt-[15px] flex justify-end">
                <button type="button" data-tutup-modal
                        class="h-[41px] w-[102px] rounded-lg bg-tosca-900 text-[14px] font-semibold text-white transition hover:bg-tosca-800">Kembali</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('skrip')
<script>
    // Buka/tutup modal: tombol dengan data-buka-modal membuka modal ber-id sama; Esc, klik latar, atau tombol Kembali menutupnya
    (function () {
        const modal = document.getElementById('modal-tracking');
        const buka = () => { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };
        const tutup = () => { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); };

        document.querySelectorAll('[data-buka-modal="modal-tracking"]').forEach((el) => el.addEventListener('click', buka));
        modal.querySelectorAll('[data-tutup-modal]').forEach((el) => el.addEventListener('click', tutup));
        modal.addEventListener('click', (e) => { if (e.target === modal || e.target === modal.firstElementChild) tutup(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) tutup(); });

        // Tautan langsung: halaman dibuka dengan modal terbuka jika alamatnya berakhiran #lacak
        if (location.hash === '#lacak') buka();
    })();
</script>
@endpush
