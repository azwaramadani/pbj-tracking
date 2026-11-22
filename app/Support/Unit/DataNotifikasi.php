<?php

namespace App\Support\Unit;

use App\Models\Notifikasi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DataNotifikasi
{
    /**
     * Mengambil seluruh notifikasi milik pengguna yang sedang login.
     */
    public static function semua(): Collection
    {
        $userId = Auth::id();

        if (!$userId) {
            return collect();
        }

        return Notifikasi::with(['pengajuan', 'pengadaan'])
            ->where('Id_user', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Notifikasi $n) {
                $dibaca = $n->Dibaca_pada !== null;

                return (object) [
                    'id' => $n->Id_notifikasi,
                    'icon' => $n->Icon ?? 'info',
                    'title' => $n->Judul,
                    'time' => $n->created_at
                        ? $n->created_at->format('d M Y, H:i') . ' WIB'
                        : '-',
                    'message' => $n->Pesan,
                    'category' => $n->Kategori,
                    'is_read' => $dibaca,
                    'status_label' => $dibaca
                        ? 'Sudah Dibaca'
                        : 'Belum Dibaca',
                    'letter_number' => $n->pengajuan?->No_surat ?? $n->pengadaan?->pengajuan?->No_surat ?? '-',
                    'permohonan_id' => $n->Id_pengajuan,
                    'pengadaan_id' => $n->Id_pengadaan,
                    'detail_url' => '#',
                ];
            });
    }

    /**
     * Tandai satu notifikasi sebagai dibaca.
     */
    public static function tandaiDibaca(int $id): void
    {
        $userId = Auth::id();

        if (!$userId) {
            return;
        }

        Notifikasi::where('Id_notifikasi', $id)
            ->where('Id_user', $userId)
            ->whereNull('Dibaca_pada')
            ->update([
                'Dibaca_pada' => now(),
            ]);
    }

    /**
     * Tandai seluruh notifikasi milik pengguna sebagai dibaca.
     */
    public static function tandaiSemuaDibaca(): void
    {
        $userId = Auth::id();

        if (!$userId) {
            return;
        }

        Notifikasi::where('Id_user', $userId)
            ->whereNull('Dibaca_pada')
            ->update([
                'Dibaca_pada' => now(),
            ]);
    }
}
