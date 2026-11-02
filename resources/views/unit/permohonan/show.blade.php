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

<!-- ===================== BARIS ATAS: INFORMASI SURAT (kiri) + LAMPIRAN & STATUS PERSETUJUAN (kanan) ===================== -->
<div class="grid grid-cols-1 gap-[26px] xl:grid-cols-[minmax(0,1fr)_376px]">

    <!-- ---------- KARTU INFORMASI SURAT PERMOHONAN ---------- -->
    <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-[23px] pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <h2 class="text-[18px] font-bold leading-6">Informasi Surat Permohonan</h2>

        <!-- Daftar pasangan label-isi: label kapital kecil abu-abu, isi teks biasa -->
        <dl class="mt-[17px] space-y-[19px]">
            @foreach ($infoSurat as [$label, $isi])
                <div>
                    <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">{{ $label }}</dt>
                    <dd class="mt-[3px] text-[14px] leading-[21px] text-gray-900">{{ $isi }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    <!-- ---------- KOLOM KANAN: LAMPIRAN DOKUMEN + DUA KARTU STATUS ---------- -->
    <div class="space-y-[14px]">

        <!-- Kartu Lampiran Dokumen: satu baris per berkas, dengan tombol unduh di kanan -->
        <section class="rounded-2xl border border-gray-100 bg-white p-[22px] shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Lampiran Dokumen</h2>

            <ul class="mt-[15px] space-y-[13px]">
                <!-- MULAI LOOP: Berkas lampiran permohonan dari database (satu iterasi = satu berkas) -->
                @foreach ($permohonan['lampiran'] as $berkas)
                    <li class="flex h-[53px] items-center gap-[10px] rounded-lg border border-gray-200 bg-[#f8fafc] pl-[13px] pr-[14px]">
                        <svg class="h-[18px] w-[18px] shrink-0 text-tosca-400" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[12px] font-bold leading-4">{{ $berkas['nama'] }}</p>
                            <p class="text-[11px] leading-4 text-gray-500">{{ $berkas['ukuran'] }}</p>
                        </div>
                        <!-- TODO Backend: arahkan tautan unduh ke route download berkas (mis. Storage::download) -->
                        <a href="#" download aria-label="Unduh {{ $berkas['nama'] }}" class="text-tosca-900 transition hover:text-tosca-600">
                            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </a>
                    </li>
                @endforeach
                <!-- AKHIR LOOP: Berkas lampiran -->
            </ul>
        </section>

        {{--
            Dua kartu status persetujuan. URUTAN TETAP: Wadir 2 dahulu, baru Perencanaan, apa pun statusnya.
            Backend hanya perlu mengisi nilai status (menunggu | disetujui | ditolak | revisi), nama penanggung jawab, dan waktu;
            warna kartu serta teks lencana ditentukan otomatis oleh komponen x-unit.kartu-persetujuan.
        --}}
        <!-- Kartu status Wadir 2 -->
        <x-unit.kartu-persetujuan
            :judul="$permohonan['persetujuan']['wadir2']['judul']"
            :subjudul="$permohonan['persetujuan']['wadir2']['subjudul']"
            :status="$permohonan['persetujuan']['wadir2']['status']"
            :penanggung-jawab="$permohonan['persetujuan']['wadir2']['penanggung_jawab']"
            :waktu="$permohonan['persetujuan']['wadir2']['waktu']" />

        <!-- Kartu status Perencanaan -->
        <x-unit.kartu-persetujuan
            :judul="$permohonan['persetujuan']['perencanaan']['judul']"
            :subjudul="$permohonan['persetujuan']['perencanaan']['subjudul']"
            :status="$permohonan['persetujuan']['perencanaan']['status']"
            :penanggung-jawab="$permohonan['persetujuan']['perencanaan']['penanggung_jawab']"
            :waktu="$permohonan['persetujuan']['perencanaan']['waktu']" />
    </div>
</div>

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
