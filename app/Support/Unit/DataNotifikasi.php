<?php

namespace App\Support\Unit;

use Illuminate\Support\Collection;

/**
 * Sumber data contoh (dummy) untuk halaman Notifikasi role User Unit.
 *
 * Status "sudah dibaca" disimpan di session agar tombol "Tandai Dibaca" dan "Tandai Semua Dibaca" bisa dicoba.
 * Saat model Eloquent tersedia, ganti isi semua() dengan query notifikasi milik pengguna dan
 * simpan status baca di kolom database (mis. read_at); controller dan view tidak perlu diubah.
 *
 * Kunci 'ikon': approval (hijau), kontrak (biru), reminder (kuning), info (abu-abu).
 */
class DataNotifikasi
{
    /** Kunci session untuk daftar id notifikasi yang sudah ditandai dibaca */
    private const KUNCI_SESSION = 'notifikasi_dibaca';

    /** Seluruh notifikasi (17 data) dengan status baca terkini */
    public static function semua(): Collection
    {
        $dibacaSesi = session(self::KUNCI_SESSION, []);

        return collect(self::dasar())->map(function (array $n) use ($dibacaSesi) {
            [$id, $ikon, $judul, $waktu, $deskripsi, $kategori, $dibaca, $nomorSurat, $permohonanId] = $n;
            $dibaca = $dibaca || in_array($id, $dibacaSesi, true);

            return (object) [
                'id'            => $id,
                'icon'          => $ikon,
                'title'         => $judul,
                'time'          => $waktu,
                'message'       => $deskripsi,
                'category'      => $kategori,
                'is_read'       => $dibaca,
                'status_label'  => $dibaca ? 'Sudah Dibaca' : 'Belum Dibaca',
                'letter_number' => $nomorSurat,
                'permohonan_id' => $permohonanId,
                // Diisi oleh controller masing-masing role (tiap role punya route detail sendiri)
                'detail_url'    => '#',
            ];
        });
    }

    /** Tandai satu notifikasi sebagai dibaca */
    public static function tandaiDibaca(int $id): void
    {
        $dibaca = session(self::KUNCI_SESSION, []);
        $dibaca[] = $id;
        session([self::KUNCI_SESSION => array_values(array_unique($dibaca))]);
    }

    /** Tandai seluruh notifikasi sebagai dibaca */
    public static function tandaiSemuaDibaca(): void
    {
        session([self::KUNCI_SESSION => self::semua()->pluck('id')->all()]);
    }

    /** Data dasar: [id, ikon, judul, waktu, deskripsi, kategori, sudahDibaca, nomor surat, id permohonan] */
    private static function dasar(): array
    {
        return [
            [1,  'approval', 'Persetujuan Permohonan Baru',            '15 Okt 2026, 08:50 WIB', 'Permohonan baru dari Unit Teknik menunggu persetujuan Wadir 2 DAN Perencanaan; persetujuan permohonan bukan gerbang PPK.', 'Approval',   false, '080/UNIT.TI/PBJ/2026', 101],
            [2,  'kontrak',  'Penerbitan Kontrak Pengadaan',           '15 Okt 2026, 08:00 WIB', 'Kontrak #KTR-2026-0078 telah diterbitkan secara resmi untuk PT Mitra Jaya terkait pengadaan perangkat lab.', 'Kontrak',     false, '078/UNIT.LAB/PBJ/2026; referensi KTR-2026-0078', 102],
            [3,  'info',     'Laporan SPJ Berhasil Diunggah',          '15 Okt 2026, 06:00 WIB', 'SPJ telah diunggah lengkap untuk proses pengadaan infrastruktur server #PRO-2026-0067.', 'Pembayaran',  true,  '067/UNIT.TI/PBJ/2026; referensi PRO-2026-0067', 103],
            [4,  'kontrak',  'Dokumen dikembalikan untuk perbaikan.',  '12 Okt 2026, 14:20 WIB', 'Untuk PPBJ: #DOC-2026-0089, Achmad Fauzi (PP) meminta revisi. Status Perlu Perbaikan; PPBJ memperbarui dokumen lalu mengajukan ulang.', 'Approval',   false, '089/UNIT.TI/PBJ/2026; dokumen DOC-2026-0089', 101],
            [5,  'approval', 'Persetujuan Permohonan Baru',            '15 Okt 2026, 08:50 WIB', 'Permohonan baru dari Unit Teknik menunggu persetujuan Wadir 2 DAN Perencanaan; persetujuan permohonan bukan gerbang PPK.', 'Approval',   false, '080/UNIT.TI/PBJ/2026', 101],
            [6,  'approval', 'Persetujuan Wadir 2 Diterima',           '11 Okt 2026, 10:15 WIB', 'Permohonan pengadaan komputer lab telah disetujui Wadir 2 dan diteruskan ke Perencanaan.', 'Approval',   false, '021/SPK/PHB-JBT/X/2026', 101],
            [7,  'approval', 'Persetujuan Perencanaan Diterima',       '10 Okt 2026, 15:40 WIB', 'Perencanaan telah menyetujui penentuan MAK untuk permohonan Anda.', 'Approval',   true,  '020/SPK/PHB-JBT/X/2026', 102],
            [8,  'approval', 'Permohonan Ditolak',                     '09 Okt 2026, 09:05 WIB', 'Permohonan ditolak oleh Wadir 2. Lihat catatan penolakan pada halaman Detail Permohonan.', 'Approval',   true,  '017/SPK/PHB-JBT/X/2026', 105],
            [9,  'reminder', 'Pengingat Kelengkapan Dokumen',          '09 Okt 2026, 08:00 WIB', 'Lengkapi lampiran RAB dan KAK sebelum batas waktu pengajuan.', 'Reminder',   false, '018/SPK/PHB-JBT/X/2026', 104],
            [10, 'info',     'Pengingat Revisi Permohonan',            '08 Okt 2026, 13:30 WIB', 'Permohonan Anda masih menunggu perbaikan. Segera ajukan ulang agar proses dapat dilanjutkan.', 'Reminder',   false, '021/SPK/PHB-JBT/X/2026', 101],
            [11, 'info',     'Pengingat Batas Waktu Pengadaan',        '07 Okt 2026, 08:00 WIB', 'Batas waktu proses pengadaan akan berakhir dalam 3 hari kerja.', 'Reminder',   true,  '019/SPK/PHB-JBT/X/2026', 103],
            [12, 'kontrak',  'Kontrak Menunggu Tanda Tangan',          '06 Okt 2026, 16:10 WIB', 'Dokumen kontrak #KTR-2026-0071 menunggu tanda tangan PPK.', 'Kontrak',     false, '071/UNIT.LAB/PBJ/2026; referensi KTR-2026-0071', 102],
            [13, 'kontrak',  'SPK Telah Diterbitkan',                  '05 Okt 2026, 11:25 WIB', 'SPK #SPK-2026-0052 telah diterbitkan untuk penyedia terpilih.', 'Kontrak',     true,  '052/UNIT.TI/PBJ/2026; referensi SPK-2026-0052', 103],
            [14, 'info',     'Pembayaran Sedang Diproses',             '04 Okt 2026, 14:00 WIB', 'Pembayaran untuk pengadaan #PRO-2026-0060 sedang diproses oleh bagian Keuangan.', 'Pembayaran',  false, '060/UNIT.TI/PBJ/2026; referensi PRO-2026-0060', 103],
            [15, 'info',     'Pembayaran Selesai',                     '03 Okt 2026, 09:45 WIB', 'Pembayaran pengadaan #PRO-2026-0055 telah selesai dilakukan.', 'Pembayaran',  true,  '055/UNIT.TI/PBJ/2026; referensi PRO-2026-0055', 103],
            [16, 'info',     'Penerimaan Barang Dikonfirmasi',         '02 Okt 2026, 17:00 WIB', 'Panitia penerima hasil pekerjaan telah mengonfirmasi penerimaan barang untuk pengadaan #PRO-2026-0049.', 'Informasi',   false, '049/UNIT.TI/PBJ/2026; referensi PRO-2026-0049', 103],
            [17, 'info',     'Berkas Lampiran Diperbarui',             '01 Okt 2026, 10:30 WIB', 'Lampiran RAB pada permohonan Anda telah diperbarui oleh Perencanaan.', 'Informasi',   true,  '045/UNIT.TI/PBJ/2026', 101],
        ];
    }
}
