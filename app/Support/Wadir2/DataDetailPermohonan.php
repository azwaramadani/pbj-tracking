<?php

namespace App\Support\Wadir2;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DataDetailPermohonan
{
    /**
     * Mengambil detail pengajuan berdasarkan ID.
     */
    public static function cari(int $id): ?array
    {
        $data = DB::table('pengajuan_surat as p')
            ->leftJoin(
                'users as u',
                'u.Id_user',
                '=',
                'p.Id_user_pemohon'
            )
            ->where('p.Id_pengajuan', $id)
            ->select([
                'p.Id_pengajuan',
                'p.No_surat',
                'p.Asal_surat',
                'p.Judul_surat',
                'p.Tujuan_pengadaan',
                'p.Nominal_pengadaan',
                'p.Status_surat',
                'u.Nama_lengkap',
                'u.NIP',
            ])
            ->first();

        if (!$data) {
            return null;
        }

        // Mengambil seluruh dokumen milik pengajuan.
        $dokumen = DB::table('dokumen_file')
            ->where('Id_pengajuan', $id)
            ->orderBy('Id_dokumen')
            ->get();

        $lampiran = $dokumen->map(function ($file) {
            $ukuran = '-';

            if (
                !empty($file->File_path) &&
                Storage::disk('public')->exists($file->File_path)
            ) {
                $bytes = Storage::disk('public')->size(
                    $file->File_path
                );

                if ($bytes >= 1048576) {
                    $ukuran = number_format(
                        $bytes / 1048576,
                        2
                    ) . ' MB';
                } elseif ($bytes >= 1024) {
                    $ukuran = number_format(
                        $bytes / 1024,
                        2
                    ) . ' KB';
                } else {
                    $ukuran = $bytes . ' B';
                }
            }

            return [
                'id' => $file->Id_dokumen,
                'nama' => basename($file->File_path),
                'ukuran' => $ukuran,
                'kategori' => $file->Kategori_dokumen,
            ];
        })->values()->all();

        // Mengambil riwayat persetujuan pengajuan.
        $approval = DB::table('riwayat_approval')
            ->where('Id_pengajuan', $id)
            ->orderByDesc('Tanggal_approval')
            ->get();

        // Mengambil catatan revisi terakhir.
        $catatanRevisi = $approval
            ->first(function ($item) {
                $status = strtolower(
                    (string) $item->Status_approval
                );

                return str_contains($status, 'revisi');
            });

        // Mengambil status persetujuan Perencanaan.
        $approvalPerencanaan = $approval
            ->first(function ($item) {
                return str_contains(
                    strtolower((string) $item->Tahapan_approval),
                    'perencanaan'
                );
            });

        $statusPerencanaan = 'menunggu';
        $waktuPerencanaan = '-';

        if ($approvalPerencanaan) {
            $statusPerencanaan = self::mapStatusApproval(
                $approvalPerencanaan->Status_approval
            );

            $waktuPerencanaan =
                $approvalPerencanaan->Tanggal_approval ?? '-';
        }

        // Format nominal pengajuan.
        $nominal = self::formatRupiah(
            $data->Nominal_pengadaan
        );

        $namaPengaju = $data->Nama_lengkap ?? '-';

        $inisial = collect(
            preg_split('/\s+/', trim($namaPengaju))
        )
            ->filter()
            ->take(2)
            ->map(fn ($kata) => mb_substr($kata, 0, 1))
            ->implode('');

        return [
            'id' => $data->Id_pengajuan,
            'nomor_surat' => $data->No_surat,
            'unit_pengaju' => $data->Asal_surat ?? '-',
            'judul' => $data->Judul_surat ?? '-',
            'tujuan' => $data->Tujuan_pengadaan ?? '-',
            'total_anggaran' => $nominal,
            'lampiran' => $lampiran,

            'pengaju' => [
                'nama' => $namaPengaju,
                'nip' => $data->NIP ?? '-',
                'inisial' => $inisial ?: '-',
            ],

            'catatan_revisi' => $catatanRevisi->Catatan ?? '-',

            'perencanaan' => [
                'judul' => 'Persetujuan Perencanaan',
                'subjudul' => 'Pemeriksaan pengajuan oleh bagian Perencanaan',
                'status' => $statusPerencanaan,
                'penanggung_jawab' => '-',
                'waktu' => $waktuPerencanaan,
            ],
        ];
    }

    /**
     * Mengubah status approval menjadi status tampilan.
     */
    private static function mapStatusApproval(?string $status): string
    {
        $status = strtolower(trim((string) $status));

        if (str_contains($status, 'revisi')) {
            return 'revisi';
        }

        if (str_contains($status, 'tolak')) {
            return 'ditolak';
        }

        if (
            str_contains($status, 'setuju') ||
            str_contains($status, 'disetujui')
        ) {
            return 'disetujui';
        }

        return 'menunggu';
    }

    /**
     * Format nominal ke rupiah.
     */
    private static function formatRupiah($nominal): string
    {
        if ($nominal === null || trim((string) $nominal) === '') {
            return '-';
        }

        $angka = preg_replace('/[^0-9]/', '', (string) $nominal);

        if ($angka === '') {
            return (string) $nominal;
        }

        return 'Rp ' . number_format(
            (float) $angka,
            0,
            ',',
            '.'
        );
    }
}
