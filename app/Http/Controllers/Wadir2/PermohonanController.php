<?php

namespace App\Http\Controllers\Wadir2;

use App\Http\Controllers\Controller;
use App\Support\Wadir2\DataDetailPermohonan;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermohonanController extends Controller
{
    protected string $prefixRute = 'wadir2';

    /**
     * Detail pengajuan Wadir 2.
     */
    public function show(int $id)
    {
        $permohonan = DataDetailPermohonan::cari($id);

        abort_if($permohonan === null, 404);

        return view('wadir2.permohonan.show', [
            'permohonan' => $permohonan,
            'prefixRute' => $this->prefixRute,
        ]);
    }


public function keputusan(Request $request, int $id)
{
    $validated = $request->validate([
        'permohonan_id' => ['required', 'integer', Rule::in([$id])],
        'keputusan' => ['required', Rule::in(['setuju', 'revisi', 'tolak'])],
        'alasan_persetujuan' => ['nullable', 'string', 'max:2000'],
    ]);

    $catatan = trim((string) ($validated['alasan_persetujuan'] ?? ''));

    // Alasan wajib diberikan jika permohonan direvisi atau ditolak.
    if (in_array($validated['keputusan'], ['revisi', 'tolak'], true) && $catatan === '') {
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

        $keputusanSebelumnya = DB::table('riwayat_approval')
            ->where('Id_pengajuan', $id)
            ->where('Tahapan_approval', 'Wadir 2')
            ->orderByDesc('Id_approval')
            ->first();

        if (
            $keputusanSebelumnya &&
            $keputusanSebelumnya->Status_approval === 'Disetujui'
        ) {
            abort(409, 'Wadir 2 sudah menyetujui permohonan ini.');
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
            'Tahapan_approval' => 'Wadir 2',
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
        ->route('wadir2.dashboard')
        ->with('success', 'Keputusan Wadir 2 berhasil disimpan.');
}
}