<?php

namespace App\Support\Perencanaan;

use Illuminate\Support\Collection;
use App\Models\PengajuanSurat;
use App\Models\Pengadaan;
use App\Models\Mak;

/**
 * Sumber data contoh (dummy) untuk role Perencanaan.
 *
 * Status permohonan: "diproses" (kuning), "selesai" (hijau), "tinjauan" (abu-abu). Detail "tinjauan" tampil sama seperti "diproses".
 * Id 701-734. Empat baris pertama persis seperti mockup Dashboard Perencanaan.
 * Saat model Eloquent tersedia, ganti isi daftar(), cari(), dan split() dengan query permohonan beserta relasi pengaju, lampiran, memo, dan anggaran.
 */
class DataPerencanaan
{
    /** Daftar permohonan untuk tabel Daftar Permohonan di Dashboard Perencanaan (34 data) */
    /**
 * Daftar permohonan untuk Dashboard Perencanaan.
 * Data diambil langsung dari tabel pengajuan_surat.
 */
public static function daftar(): Collection
{
    return PengajuanSurat::with('pemohon')
        ->where('Status_surat', '!=', 'Draft')
        ->orderByDesc('Id_pengajuan')
        ->get()
        ->map(function (PengajuanSurat $pengajuan) {

            // Mengambil asal unit dari akun pemohon.
            $unit = $pengajuan->pemohon?->Asal_unit
                ?: $pengajuan->Asal_surat
                ?: '-';

            // Mengubah nominal string menjadi format Rupiah.
            $nilai = is_numeric($pengajuan->Nominal_pengadaan)
                ? 'Rp ' . number_format(
                    (float) $pengajuan->Nominal_pengadaan,
                    0,
                    ',',
                    '.'
                )
                : '-';

            // Pemetaan status database ke status frontend.
            $status = match ($pengajuan->Status_surat) {
                'Menunggu Persetujuan Awal' => 'diproses',
                default => 'tinjauan',
            };

            return [
                'id'       => $pengajuan->Id_pengajuan,
                'no_surat' => $pengajuan->No_surat,
                'tgl_surat' => $pengajuan->Tanggal_surat
                    ? $pengajuan->Tanggal_surat->format('d-m-Y')
                    : '-',
                'unit'     => $unit,
                'judul'    => $pengajuan->Judul_surat,
                'nilai'    => $nilai,
                'status'   => $status,
            ];
        });
}

    /** Opsi "Jenis Anggaran" pada Form Alokasi Anggaran (MAK) */
    public static function jenisAnggaran(): array
{
    return Mak::query()
        ->whereNotNull('Jenis_anggaran')
        ->where('Jenis_anggaran', '<>', '')
        ->distinct()
        ->orderBy('Jenis_anggaran')
        ->pluck('Jenis_anggaran')
        ->values()
        ->all();
}

    /** Satu permohonan lengkap untuk halaman Detail Permohonan; null jika tidak ditemukan */
    
/**
 * Satu permohonan lengkap untuk halaman Detail Permohonan;
 * null jika tidak ditemukan.
 */
public static function cari(int $id): ?array
{
    $pengajuan = PengajuanSurat::with([
        'pemohon',
        'dokumen',
        'pengadaan.mak',
    ])
        ->where('Status_surat', '!=', 'Draft')
        ->find($id);

    if ($pengajuan === null) {
        return null;
    }

    // Identitas pemohon
    $namaPengaju = $pengajuan->pemohon?->Nama_lengkap ?: '-';
    $nipPengaju = $pengajuan->pemohon?->NIP ?: '-';
    $asalUnit = $pengajuan->pemohon?->Asal_unit
        ?: $pengajuan->Asal_surat
        ?: '-';

    // Membuat inisial dari nama pemohon
    $inisial = collect(
        preg_split('/\s+/', trim($namaPengaju))
    )
        ->filter()
        ->map(fn ($kata) => strtoupper(substr($kata, 0, 1)))
        ->take(2)
        ->implode('');

    // Format nominal pengajuan
    $totalAnggaran = is_numeric($pengajuan->Nominal_pengadaan)
        ? 'Rp ' . number_format(
            (float) $pengajuan->Nominal_pengadaan,
            2,
            ',',
            '.'
        )
        : '-';

    // Mengambil dokumen dari disk public
    $lampiran = $pengajuan->dokumen->map(function ($dokumen) {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        $tersedia = $disk->exists($dokumen->File_path);

        $ukuran = '-';

        if ($tersedia) {
            $bytes = $disk->size($dokumen->File_path);

            $ukuran = $bytes >= 1048576
                ? number_format($bytes / 1048576, 1) . ' MB'
                : number_format($bytes / 1024, 0) . ' KB';
        }

        return [
            'id' => $dokumen->Id_dokumen,
            'nama' => basename($dokumen->File_path),
            'kategori' => $dokumen->Kategori_dokumen,
            'ukuran' => $ukuran,
            'path' => $dokumen->File_path,
        ];
    })->values()->toArray();

    // Status pengajuan mengikuti status yang digunakan dashboard
    $status = match ($pengajuan->Status_surat) {
        'Menunggu Persetujuan Awal' => 'diproses',
        default => 'tinjauan',
    };

    // Data alokasi MAK hanya diambil dari paket pengadaan yang tersimpan.
    // Struktur halaman saat ini masih mendukung satu alokasi.
    $paket = $pengajuan->pengadaan->count() === 1
        ? $pengajuan->pengadaan->first()
        : null;

    $anggaran = [
        'jenis_anggaran' => $paket?->mak?->Jenis_anggaran ?? '-',
        'mata_anggaran' => $paket && $paket->mak
            ? $paket->mak->Kode_MAK . ' - ' . $paket->mak->Nama_MAK
            : '-',
        'jumlah_alokasi' => $paket
            ? number_format((float) $paket->Nominal_anggaran, 0, ',', '.')
            : '-',
    ];

    // Memo harus berasal dari dokumen yang benar-benar tersimpan.
    // Belum ada dokumen memo pada data pengajuan ini.
    $memo = [];

    return [
        'id' => $pengajuan->Id_pengajuan,
        'status' => $status,

        'nomor_surat' => $pengajuan->No_surat ?: '-',
        'unit_pengaju' => $asalUnit,
        'judul' => $pengajuan->Judul_surat ?: '-',
        'tujuan' => $pengajuan->Tujuan_pengadaan ?: '-',
        'total_anggaran' => $totalAnggaran,

        'lampiran' => $lampiran,

        'pengaju' => [
            'nama' => $namaPengaju,
            'nip' => $nipPengaju,
            'inisial' => $inisial ?: '-',
        ],

        'anggaran' => $anggaran,
        'memo' => $memo,
    ];
}


    /** Opsi Mata Anggaran Kegiatan untuk halaman Split MAK; 'kategori' dipakai pada label dokumen turunan di diagram */
   
/**
 * Opsi Mata Anggaran Kegiatan berdasarkan master MAK.
 */
public static function opsiMak(): array
{
    return Mak::query()
        ->whereNotNull('Kode_MAK')
        ->whereNotNull('Nama_MAK')
        ->orderBy('Kode_MAK')
        ->get([
            'Kode_MAK',
            'Nama_MAK',
            'Jenis_anggaran',
        ])
        ->map(function (Mak $mak) {
            return [
                'kode' => $mak->Kode_MAK,
                'label' => $mak->Kode_MAK . ' - ' . $mak->Nama_MAK,
                'kategori' => $mak->Jenis_anggaran ?: '-',
            ];
        })
        ->values()
        ->all();
}

    /** Data halaman Split MAK untuk satu permohonan; null jika tidak ditemukan */
    
public static function split(int $id): ?array
{
    $pengajuan = PengajuanSurat::with([
        'pengadaan.mak',
    ])
        ->where('Status_surat', '!=', 'Draft')
        ->find($id);

    if ($pengajuan === null) {
        return null;
    }

    $total = is_numeric($pengajuan->Nominal_pengadaan)
        ? (int) $pengajuan->Nominal_pengadaan
        : 0;

    $status = match ($pengajuan->Status_surat) {
        'Ditolak' => 'ditolak',
        'Selesai' => 'selesai',
        default => 'diproses',
    };

    $baris = $pengajuan->pengadaan
        ->map(function (Pengadaan $paket) {
            return [
                'kode' => $paket->mak?->Kode_MAK ?? '',
                'nominal' => (int) $paket->Nominal_anggaran,
            ];
        })
        ->values()
        ->all();

    $opsiMak = self::opsiMak();

    return [
        'id' => $pengajuan->Id_pengajuan,
        'status' => $status,
        'kode_induk' => $pengajuan->No_surat,
        'total' => $total,
        'opsi_mak' => $opsiMak,
        'baris' => $baris,
    ];
}
}
