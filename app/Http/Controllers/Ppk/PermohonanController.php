<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Wadir2\PermohonanController as PermohonanWadir2;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermohonanController extends PermohonanWadir2
{
    protected string $prefixRute = 'ppk';

    public function keputusan(Request $request, int $id)
    {
        $validated = $request->validate([
            'permohonan_id' => ['required', 'integer', Rule::in([$id])],
            'keputusan' => ['required', Rule::in(['setuju', 'revisi', 'tolak'])],
            'alasan_persetujuan' => ['nullable', 'string', 'max:2000'],
        ]);

        $catatan = trim((string) ($validated['alasan_persetujuan'] ?? ''));

        if (
            in_array($validated['keputusan'], ['revisi', 'tolak'], true)
            && $catatan === ''
        ) {
            return back()
                ->withErrors([
                    'alasan_persetujuan' => 'Alasan revisi atau penolakan wajib diisi.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($id, $validated, $catatan) {
            $pengajuan = PengajuanSurat::where('Id_pengajuan', $id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pengajuan->Status_surat !== 'Menunggu Persetujuan Awal') {
                abort(409, 'Permohonan ini tidak sedang menunggu persetujuan awal.');
            }

            // Periksa keputusan PPK, bukan keputusan Wadir 2.
            $keputusanSebelumnya = DB::table('riwayat_approval')
                ->where('Id_pengajuan', $id)
                ->where('Tahapan_approval', 'PPK')
                ->orderByDesc('Id_approval')
                ->first();

            if ($keputusanSebelumnya) {
                abort(409, 'PPK sudah memberikan keputusan untuk permohonan ini.');
            }

            $statusApproval = match ($validated['keputusan']) {
                'setuju' => 'Disetujui',
                'revisi' => 'Revisi',
                'tolak' => 'Ditolak',
            };

            DB::table('riwayat_approval')->insert([
                'Id_pengadaan' => null,
                'Id_pengajuan' => $pengajuan->Id_pengajuan,
                'Id_user_approver' => Auth::id(),
                'Id_user_uploader' => $pengajuan->Id_user_pemohon,
                'Tahapan_approval' => 'PPK',
                'Tanggal_approval' => now()->toDateString(),
                'Catatan' => $catatan !== '' ? $catatan : null,
                'Status_approval' => $statusApproval,
            ]);

            if (in_array($statusApproval, ['Revisi', 'Ditolak'], true)) {
                $pengajuan->Status_surat = $statusApproval;
                $pengajuan->save();
            }
        });

        return redirect()
            ->route('ppk.dashboard')
            ->with('success', 'Keputusan PPK berhasil disimpan.');
    }
}
