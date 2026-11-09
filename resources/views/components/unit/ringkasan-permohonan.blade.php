{{--
    Ringkasan Detail Permohonan (dipakai bersama oleh Detail Permohonan User Unit dan tahap 1-3 Tracking PPK):
    kartu "Informasi Surat Permohonan" (kiri) + kartu "Lampiran Dokumen" dan kartu status persetujuan (kanan).

    Props:
      baris    : pasangan [label, isi] untuk kartu Informasi Surat Permohonan
      lampiran : daftar berkas, tiap item ['nama' => string, 'ukuran' => string]
      kartu    : kartu status persetujuan, urutan mengikuti isi array; tiap item
                 ['judul', 'subjudul', 'status' (menunggu|disetujui|ditolak|revisi), 'penanggung_jawab', 'waktu']
--}}
@props(['baris', 'lampiran', 'kartu'])

<!-- ===================== BARIS ATAS: INFORMASI SURAT (kiri) + LAMPIRAN & STATUS PERSETUJUAN (kanan) ===================== -->
<div class="grid grid-cols-1 gap-[26px] xl:grid-cols-[minmax(0,1fr)_376px]">

    <!-- ---------- KARTU INFORMASI SURAT PERMOHONAN ---------- -->
    <section class="rounded-2xl border border-gray-100 bg-white px-6 pb-[23px] pt-6 shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
        <h2 class="text-[18px] font-bold leading-6">Informasi Surat Permohonan</h2>

        <!-- Daftar pasangan label-isi: label kapital kecil abu-abu, isi teks biasa -->
        <dl class="mt-[17px] space-y-[19px]">
            @foreach ($baris as [$label, $isi])
                <div>
                    <dt class="text-[12px] font-medium uppercase leading-4 text-gray-500">{{ $label }}</dt>
                    <dd class="mt-[3px] text-[14px] leading-[21px] text-gray-900">{{ $isi }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    <!-- ---------- KOLOM KANAN: LAMPIRAN DOKUMEN + KARTU STATUS ---------- -->
    <div class="space-y-[14px]">

        <!-- Kartu Lampiran Dokumen: satu baris per berkas, dengan tombol unduh di kanan -->
        <section class="rounded-2xl border border-gray-100 bg-white p-[22px] shadow-[0_1px_3px_rgba(15,23,42,0.06)]">
            <h2 class="text-[16px] font-bold leading-6">Lampiran Dokumen</h2>

            <ul class="mt-[15px] space-y-[13px]">
                <!-- MULAI LOOP: Berkas lampiran permohonan dari database (satu iterasi = satu berkas) -->
                @foreach ($lampiran as $berkas)
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
            Kartu status persetujuan, ditampilkan sesuai urutan array $kartu (urutan tetap untuk satu halaman).
            Backend hanya perlu mengisi nilai status (menunggu | disetujui | ditolak | revisi), nama penanggung jawab, dan waktu;
            warna kartu serta teks lencana ditentukan otomatis oleh komponen x-unit.kartu-persetujuan.
        --}}
        <!-- MULAI LOOP: Kartu status persetujuan (satu iterasi = satu pihak, mis. Wadir 2 / Perencanaan / PPK) -->
        @foreach ($kartu as $k)
            <x-unit.kartu-persetujuan
                :judul="$k['judul']"
                :subjudul="$k['subjudul']"
                :status="$k['status']"
                :penanggung-jawab="$k['penanggung_jawab']"
                :waktu="$k['waktu']" />
        @endforeach
        <!-- AKHIR LOOP: Kartu status persetujuan -->
    </div>
</div>
