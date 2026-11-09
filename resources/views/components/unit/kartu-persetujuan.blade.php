{{--
    Kartu status persetujuan satu pihak (Wadir 2 atau Perencanaan) pada halaman Detail Permohonan.
    Komponen ini dipakai berulang agar urutan dan tampilan kedua kartu selalu konsisten.

    Props:
      judul            : nama pihak, contoh "Wadir 2"
      subjudul         : keterangan kecil di bawah nama
      status           : menunggu | disetujui | ditolak | revisi  (menentukan warna kartu dan teks lencana)
      penanggungJawab  : nama penanggung jawab
      waktu            : teks tanggal & waktu, contoh "15 Februari 2026" atau "Belum disetujui"
--}}
@props(['judul', 'subjudul', 'status' => 'menunggu', 'penanggungJawab', 'waktu', 'kelasKolom' => 'grid-cols-[155px_1fr]'])

@php
    /*
     * Peta status => kelas warna Tailwind + label lencana.
     * TODO Backend: cukup kirim salah satu kunci status di atas; warna dan teks lencana mengikuti otomatis.
     */
    $gaya = [
        'ditolak'   => ['label' => 'Ditolak',   'kartu' => 'border-[#f4dde0] to-[#fde3e6]', 'ikon' => 'bg-[#f9e2e6] text-[#d4515f]', 'lencana' => 'bg-[#fde1df] text-[#b4232c]', 'hiasan' => 'bg-[#fbd5da]'],
        'revisi'    => ['label' => 'Revisi',    'kartu' => 'border-[#e7e1f7] to-[#e9e3fd]', 'ikon' => 'bg-[#eadcfb] text-[#8b3fd1]', 'lencana' => 'bg-[#f1e4ff] text-[#6d28d9]', 'hiasan' => 'bg-[#ddd3fa]'],
        'menunggu'  => ['label' => 'Menunggu',  'kartu' => 'border-[#f4ead0] to-[#fdf0c9]', 'ikon' => 'bg-[#fdf0c8] text-[#e0a41a]', 'lencana' => 'bg-[#fdeec3] text-[#b97a05]', 'hiasan' => 'bg-[#fbe7a8]'],
        'disetujui' => ['label' => 'Disetujui', 'kartu' => 'border-[#d6efe3] to-[#d8f3e7]', 'ikon' => 'bg-[#d9f5e6] text-[#22a263]', 'lencana' => 'bg-[#d3f5e3] text-[#1c8a52]', 'hiasan' => 'bg-[#bfeed6]'],
    ][$status] ?? null;

    abort_if($gaya === null, 500, 'Status persetujuan tidak dikenal: ' . $status);
@endphp

<!-- Kartu status: latar gradien putih ke warna status di sisi kanan -->
<div class="relative h-[119px] overflow-hidden rounded-2xl border bg-gradient-to-r from-white via-white {{ $gaya['kartu'] }} p-[14px] shadow-[0_1px_3px_rgba(15,23,42,0.05)]">

    <!-- Hiasan lingkaran transparan di pojok kanan bawah (murni dekorasi) -->
    <span class="pointer-events-none absolute -bottom-12 -right-8 h-[110px] w-[110px] rounded-full opacity-60 {{ $gaya['hiasan'] }}"></span>

    <!-- Baris atas: ikon pihak, nama + keterangan, dan lencana status di kanan -->
    <div class="relative flex items-start gap-[11px]">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $gaya['ikon'] }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
        </span>
        <div class="min-w-0 flex-1 pt-[1px]">
            <p class="text-[15px] font-bold leading-5">{{ $judul }}</p>
            <p class="mt-[3px] text-[10px] leading-[14px] text-gray-400">{{ $subjudul }}</p>
        </div>
        <!-- Lencana status: teks dan warna diambil dari peta status -->
        <span class="mt-[6px] inline-flex h-[22px] w-[78px] items-center justify-center rounded-full text-[10px] font-semibold {{ $gaya['lencana'] }}">{{ $gaya['label'] }}</span>
    </div>

    <!-- Baris bawah: penanggung jawab dan tanggal & waktu persetujuan -->
    <div class="relative mt-[11px] grid {{ $kelasKolom }} pl-[55px]">
        <div>
            <p class="text-[10px] font-bold uppercase leading-4 tracking-wide text-gray-500">Penanggung Jawab</p>
            <p class="mt-[2px] text-[12.5px] leading-5 text-gray-800">{{ $penanggungJawab }}</p>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase leading-4 tracking-wide text-gray-500">Tanggal &amp; Waktu</p>
            <p class="mt-[2px] text-[12.5px] leading-5 text-gray-800">{{ $waktu }}</p>
        </div>
    </div>
</div>
