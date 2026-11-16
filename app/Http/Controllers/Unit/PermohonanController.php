<?php

namespace App\Http\Controllers\Unit;

use App\Models\PengajuanSurat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Support\Unit\DataPermohonan;
use Illuminate\Http\Request;

class PermohonanController extends Controller
{
    /** Jumlah baris per halaman pada Daftar Permohonan */
    private const PER_HALAMAN = 8;

    /** Kunci filter status yang diizinkan pada query string ?status= */
    private const FILTER_STATUS = ['semua', 'draft', 'menunggu', 'diproses', 'selesai'];

    /**
     * Daftar Permohonan Saya.
     * Mendukung filter tab (?status=), pencarian (?q=), dan paginasi (?page=) di sisi server,
     * sehingga URL dapat dibagikan dan nanti mudah dialihkan ke query Eloquent.
     */
    
public function index(Request $request)
{
    $status = in_array($request->query('status'), self::FILTER_STATUS, true)
        ? $request->query('status')
        : 'semua';

    $kataKunci = trim((string) $request->query('q', ''));

    $idUser = auth()->user()->Id_user;

    // Pemetaan status tampilan ke status database.
    $pemetaanStatus = [
        'draft' => 'Draft',
        'menunggu' => 'Menunggu Persetujuan Awal',
    ];

    // Query dasar hanya untuk permohonan milik pengguna login.
    $queryDasar = PengajuanSurat::query()
        ->where('Id_user_pemohon', $idUser);

    // Menghitung jumlah data setiap tab.
    $jumlahTab = [
        'semua' => (clone $queryDasar)->count(),
        'draft' => (clone $queryDasar)
            ->where('Status_surat', 'Draft')
            ->count(),
        'menunggu' => (clone $queryDasar)
            ->where('Status_surat', 'Menunggu Persetujuan Awal')
            ->count(),
        'diproses' => 0,
        'selesai' => 0,
    ];

    $query = clone $queryDasar;

    // Filter status.
    if ($status !== 'semua') {
        if (isset($pemetaanStatus[$status])) {
            $query->where('Status_surat', $pemetaanStatus[$status]);
        } else {
            $query->whereRaw('1 = 0');
        }
    }

    // Pencarian nomor surat dan judul pengadaan.
    if ($kataKunci !== '') {
        $query->where(function ($subQuery) use ($kataKunci) {
            $subQuery
                ->where('No_surat', 'ILIKE', "%{$kataKunci}%")
                ->orWhere('Judul_surat', 'ILIKE', "%{$kataKunci}%");
        });
    }

    // Mengambil data terbaru dengan paginasi.
    $permohonan = $query
        ->orderByDesc('Id_pengajuan')
        ->paginate(self::PER_HALAMAN)
        ->withQueryString();

    return view('unit.permohonan.index', [
        'permohonan' => $permohonan,
        'jumlahTab' => $jumlahTab,
        'statusAktif' => $status,
        'kataKunci' => $kataKunci,
    ]);
}


    /** Formulir Revisi Pengajuan: formulir terisi data lama + kotak Permintaan Perbaikan */
   
public function revisi(int $id)
{
    // Hanya pemilik pengajuan berstatus Draft yang boleh mengedit.
    $pengajuan = PengajuanSurat::with('dokumen')
        ->where('Id_user_pemohon', auth()->user()->Id_user)
        ->where('Status_surat', 'Draft')
        ->findOrFail($id);

    // Ambil dokumen lama berdasarkan kategori.
    $dokumenLama = $pengajuan->dokumen
        ->keyBy('Kategori_dokumen');

    $permohonan = [
        'id' => $pengajuan->Id_pengajuan,
        'no_surat' => $pengajuan->No_surat,
        'judul' => $pengajuan->Judul_surat,
        'nilai_anggaran' => $pengajuan->Nominal_pengadaan,
        'deskripsi' => $pengajuan->Tujuan_pengadaan,
        'asal_unit' => $pengajuan->Asal_surat,

        'dokumen' => [
            'rab' => $dokumenLama->get('RAB'),
            'kak' => $dokumenLama->get('KAK'),
            'spesifikasi' => $dokumenLama->get('Spesifikasi'),
        ],

        // Draft belum memiliki catatan permintaan revisi dari peninjau.
        'permintaan_perbaikan' => [
            'diminta_oleh' => '-',
            'peninjau' => '-',
            'waktu' => '-',
            'penanggung_jawab' => '-',
            'catatan' => 'Pengajuan ini masih berstatus Draft dan dapat dilengkapi sebelum dikirim.',
        ],
    ];

    return view('unit.permohonan.revisi', [
        'permohonan' => $permohonan,
    ]);
}


    /** Detail Permohonan berdasarkan id */
    
public function show(int $id)
{
    // 1. Ambil pengajuan milik pengguna yang sedang login.
    $pengajuan = PengajuanSurat::with('dokumen')
        ->where('Id_user_pemohon', auth()->user()->Id_user)
        ->findOrFail($id);

    // 2. Ambil riwayat persetujuan berdasarkan pengajuan.
    $riwayatApproval = DB::table('riwayat_approval')
        ->leftJoin(
            'users',
            'riwayat_approval.Id_user_approver',
            '=',
            'users.Id_user'
        )
        ->where('riwayat_approval.Id_pengajuan', $id)
        ->select(
            'riwayat_approval.*',
            'users.Nama_lengkap'
        )
        ->orderBy('riwayat_approval.Tanggal_approval')
        ->orderBy('riwayat_approval.Id_approval')
        ->get();

    // 3. Pemetaan status database ke status komponen frontend.
    $ubahStatus = function ($status) {
        $status = mb_strtolower(trim((string) $status));

        if (str_contains($status, 'belum disetujui')) {
            return 'menunggu';
        }

        if (
            str_contains($status, 'tidak disetujui') ||
            str_contains($status, 'tolak') ||
            str_contains($status, 'reject')
        ) {
            return 'ditolak';
        }

        if (
            str_contains($status, 'revisi') ||
            str_contains($status, 'perbaikan')
        ) {
            return 'revisi';
        }

        if (
            str_contains($status, 'setuju') ||
            str_contains($status, 'approved') ||
            str_contains($status, 'accept')
        ) {
            return 'disetujui';
        }

        return 'menunggu';
    };

    // 4. Cari approval terakhir berdasarkan nama tahapan.
    $approvalTerakhir = function ($kataKunci) use ($riwayatApproval) {
        return $riwayatApproval
            ->filter(function ($item) use ($kataKunci) {
                return str_contains(
                    mb_strtolower((string) $item->Tahapan_approval),
                    $kataKunci
                );
            })
            ->last();
    };

    $approvalWadir = $approvalTerakhir('wadir');
    $approvalPerencanaan = $approvalTerakhir('perencanaan');

    // 5. Format nominal pengadaan.
    $nominal = preg_replace(
        '/\D+/',
        '',
        (string) $pengajuan->Nominal_pengadaan
    );

    $nominalFormat = $nominal !== ''
        ? 'Rp ' . number_format((int) $nominal, 0, ',', '.')
        : '-';

    // 6. Format tanggal pengajuan.
    $tanggalPengajuan = $pengajuan->Tanggal_surat
        ? $pengajuan->Tanggal_surat->format('d/m/Y')
        : '-';

    // 7. Ambil informasi dokumen lampiran.
    $lampiran = $pengajuan->dokumen->map(function ($dokumen) {
        $disk = Storage::disk('public');

        $ukuran = $disk->exists($dokumen->File_path)
            ? $disk->size($dokumen->File_path)
            : 0;

        return [
            'nama' => basename($dokumen->File_path),
            'ukuran' => $ukuran > 0
                ? number_format($ukuran / 1024, 1, ',', '.') . ' KB'
                : 'Ukuran tidak tersedia',
        ];
    })->values()->all();

    // 8. Data kartu persetujuan Wadir 2.
    $dataWadir = [
        'judul' => 'Persetujuan Wadir 2',
        'subjudul' => 'Pemeriksaan dan persetujuan awal',
        'status' => $approvalWadir
            ? $ubahStatus($approvalWadir->Status_approval)
            : 'menunggu',
        'penanggung_jawab' => $approvalWadir->Nama_lengkap
            ?? 'Wadir 2',
        'waktu' => $approvalWadir && $approvalWadir->Tanggal_approval
            ? \Carbon\Carbon::parse($approvalWadir->Tanggal_approval)
                ->format('d/m/Y H:i')
            : '-',
    ];

    // 9. Data kartu persetujuan Perencanaan.
    $dataPerencanaan = [
        'judul' => 'Pemeriksaan Perencanaan',
        'subjudul' => 'Pemeriksaan anggaran dan penentuan MAK',
        'status' => $approvalPerencanaan
            ? $ubahStatus($approvalPerencanaan->Status_approval)
            : 'menunggu',
        'penanggung_jawab' => $approvalPerencanaan->Nama_lengkap
            ?? 'Tim Perencanaan',
        'waktu' => $approvalPerencanaan && $approvalPerencanaan->Tanggal_approval
            ? \Carbon\Carbon::parse($approvalPerencanaan->Tanggal_approval)
                ->format('d/m/Y H:i')
            : '-',
    ];

    // 10. Data tabel riwayat aktivitas.
    $riwayat = $riwayatApproval->map(function ($item) {
        return [
            'waktu' => $item->Tanggal_approval
                ? \Carbon\Carbon::parse($item->Tanggal_approval)
                    ->format('d/m/Y H:i')
                : '-',

            'user' => $item->Nama_lengkap ?? '-',
            'role' => $item->Tahapan_approval ?? '-',
            'aktivitas' => 'Proses persetujuan',
            'sebelumnya' => '-',
            'baru' => $item->Status_approval ?? '-',
            'catatan' => $item->Catatan ?? '-',
        ];
    })->values()->all();

    // 11. Susun data sesuai kebutuhan show.blade.php.
    $permohonan = [
        'id' => $pengajuan->Id_pengajuan,
        'no_surat' => $pengajuan->No_surat,
        'judul' => $pengajuan->Judul_surat,
        'nilai_anggaran' => $nominalFormat,
        'unit' => $pengajuan->Asal_surat,
        'deskripsi' => $pengajuan->Tujuan_pengadaan,
        'tgl_kirim_lengkap' => $tanggalPengajuan,
        'lampiran' => $lampiran,

        'persetujuan' => [
            'wadir2' => $dataWadir,
            'perencanaan' => $dataPerencanaan,
        ],

        'riwayat' => $riwayat,
    ];

    // 12. Tentukan status timeline.
    $statusSurat = $pengajuan->Status_surat;

    $wadirState = $approvalWadir
        ? $ubahStatus($approvalWadir->Status_approval)
        : (
            $statusSurat === 'Menunggu Persetujuan Awal'
                ? 'aktif'
                : 'menunggu'
        );

    $perencanaanState = $approvalPerencanaan
        ? $ubahStatus($approvalPerencanaan->Status_approval)
        : (
            $wadirState === 'disetujui'
                ? 'aktif'
                : 'menunggu'
        );

    // 13. Susun timeline tahap awal.
    $alur = [
        [
            'state' => $statusSurat === 'Draft' ? 'aktif' : 'selesai',
            'nomor' => '01.',
            'judul' => 'Pengajuan Permohonan',
            'peran' => 'User Unit',
            'bullet' => true,
            'keterangan' => 'Unit mengisi dan mengirim surat permohonan pengadaan.',
        ],
        [
            'state' => $wadirState,
            'nomor' => '02.',
            'judul' => 'Persetujuan Awal',
            'peran' => 'Wadir 2',
            'bullet' => true,
            'keterangan' => 'Pemeriksaan dan persetujuan awal permohonan.',
        ],
        [
            'state' => $perencanaanState,
            'nomor' => '03.',
            'judul' => 'Pemeriksaan Anggaran',
            'peran' => 'Perencanaan',
            'bullet' => true,
            'keterangan' => 'Pemeriksaan anggaran dan penentuan MAK.',
        ],
        [
            'state' => 'menunggu',
            'nomor' => '04.',
            'judul' => 'Proses Pengadaan',
            'peran' => 'PPBJ',
            'bullet' => true,
            'keterangan' => 'Menunggu penyelesaian pemeriksaan perencanaan.',
        ],
    ];

    return view('unit.permohonan.show', [
        'permohonan' => $permohonan,
        'alur' => $alur,
    ]);
}



/**
 * Menyimpan draf atau mengirim pengajuan permohonan.
 */
public function store(Request $request)
{
    $request->validate([
        'aksi' => ['required', Rule::in(['draf', 'kirim'])],

        'nomor_surat' => [
            'required',
            'string',
            'max:255',
            'unique:pengajuan_surat,No_surat',
        ],

        'judul_pengadaan' => ['required', 'string', 'max:255'],
        'asal_unit' => ['required', 'string', 'max:255'],
        'alasan_kebutuhan' => ['required', 'string', 'max:255'],
        'nilai_anggaran' => ['nullable', 'string', 'max:255'],

        'rab' => [
            Rule::requiredIf($request->aksi === 'kirim'),
            'nullable',
            'file',
            'mimes:pdf,xlsx,zip',
            'max:10240',
        ],

        'kak' => [
            Rule::requiredIf($request->aksi === 'kirim'),
            'nullable',
            'file',
            'mimes:pdf,xlsx,zip',
            'max:10240',
        ],

        'spesifikasi' => [
            Rule::requiredIf($request->aksi === 'kirim'),
            'nullable',
            'file',
            'mimes:pdf,xlsx,zip',
            'max:10240',
        ],
    ], [
        'rab.required' => 'Dokumen RAB wajib diunggah sebelum pengajuan dikirim.',
        'kak.required' => 'Dokumen KAK wajib diunggah sebelum pengajuan dikirim.',
        'spesifikasi.required' => 'Dokumen Spesifikasi wajib diunggah sebelum pengajuan dikirim.',
        'nomor_surat.unique' => 'Nomor surat tersebut sudah pernah digunakan.',
    ]);

    $fileTersimpan = [];

    try {
        DB::transaction(function () use ($request, &$fileTersimpan) {

            // Menentukan status berdasarkan tombol yang ditekan.
            $status = $request->aksi === 'draf'
                ? 'Draft'
                : 'Menunggu Persetujuan Awal';

            // Membuat data pengajuan.
            $pengajuan = PengajuanSurat::create([
                'Id_user_pemohon' => auth()->user()->Id_user,
                'Tujuan_pengadaan' => $request->alasan_kebutuhan,
                'Status_surat' => $status,
                'Asal_surat' => $request->asal_unit,
                'Tanggal_surat' => now()->toDateString(),
                'Judul_surat' => $request->judul_pengadaan,
                'No_surat' => $request->nomor_surat,
                'Nominal_pengadaan' => $request->nilai_anggaran,
            ]);

            // Menyimpan setiap dokumen yang diunggah.
            $kategoriDokumen = [
                'rab' => 'RAB',
                'kak' => 'KAK',
                'spesifikasi' => 'Spesifikasi',
            ];

            foreach ($kategoriDokumen as $field => $kategori) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);

                    $path = $file->store(
                        'dokumen-pengajuan',
                        'public'
                    );

                    $fileTersimpan[] = $path;

                    $pengajuan->dokumen()->create([
                        'Id_pengadaan' => null,
                        'Tanggal_dokumen' => now()->toDateString(),
                        'File_path' => $path,
                        'Kategori_dokumen' => $kategori,
                    ]);
                }
            }
        });
    } catch (\Throwable $e) {
        // Membersihkan file jika transaksi database gagal.
        if (!empty($fileTersimpan)) {
            Storage::disk('public')->delete($fileTersimpan);
        }

        throw $e;
    }

    if ($request->aksi === 'draf') {
        return redirect()
            ->route('unit.permohonan.create')
            ->with('success', 'Draf permohonan berhasil disimpan.');
    }

    return redirect()
        ->route('unit.permohonan.create')
        ->with('success', 'Permohonan berhasil dikirim dan menunggu persetujuan awal.');
}

    


public function update(Request $request, int $id)
{
    // Ambil hanya pengajuan milik pengguna yang login dan masih Draft.
    $pengajuan = PengajuanSurat::with('dokumen')
        ->where('Id_user_pemohon', auth()->user()->Id_user)
        ->where('Status_surat', 'Draft')
        ->findOrFail($id);

    $request->validate([
        'aksi' => ['required', Rule::in(['draf', 'kirim'])],

        'nomor_surat' => [
            'required',
            'string',
            'max:255',
            Rule::unique('pengajuan_surat', 'No_surat')
                ->ignore($pengajuan->Id_pengajuan, 'Id_pengajuan'),
        ],

        'judul_pengadaan' => ['required', 'string', 'max:255'],
        'asal_unit' => ['required', 'string', 'max:255'],
        'alasan_kebutuhan' => ['required', 'string', 'max:255'],
        'nilai_anggaran' => ['nullable', 'string', 'max:255'],

        'rab' => ['nullable', 'file', 'mimes:pdf,xlsx,zip', 'max:10240'],
        'kak' => ['nullable', 'file', 'mimes:pdf,xlsx,zip', 'max:10240'],
        'spesifikasi' => ['nullable', 'file', 'mimes:pdf,xlsx,zip', 'max:10240'],
    ]);

    $kategoriDokumen = [
        'rab' => 'RAB',
        'kak' => 'KAK',
        'spesifikasi' => 'Spesifikasi',
    ];

    // Periksa dokumen yang sudah tersimpan.
    $dokumenLama = $pengajuan->dokumen->keyBy('Kategori_dokumen');

    // Jika akan dikirim, setiap kategori wajib memiliki dokumen.
    if ($request->aksi === 'kirim') {
        $pesanError = [];

        foreach ($kategoriDokumen as $field => $kategori) {
            $sudahAda = $dokumenLama->has($kategori);
            $adaUploadBaru = $request->hasFile($field);

            if (!$sudahAda && !$adaUploadBaru) {
                $pesanError[$field] = "Dokumen {$kategori} wajib diunggah sebelum permohonan dikirim.";
            }
        }

        if (!empty($pesanError)) {
            return back()
                ->withErrors($pesanError)
                ->withInput();
        }
    }

    $fileTersimpan = [];
    $fileLamaUntukDihapus = [];

    try {
        DB::transaction(function () use (
            $request,
            $pengajuan,
            $kategoriDokumen,
            $dokumenLama,
            &$fileTersimpan,
            &$fileLamaUntukDihapus
        ) {
            // Perbarui informasi pengajuan yang sudah ada.
            $pengajuan->update([
                'Tujuan_pengadaan' => $request->alasan_kebutuhan,
                'Asal_surat' => $request->asal_unit,
                'Judul_surat' => $request->judul_pengadaan,
                'No_surat' => $request->nomor_surat,
                'Nominal_pengadaan' => $request->nilai_anggaran,
                'Status_surat' => $request->aksi === 'draf'
                    ? 'Draft'
                    : 'Menunggu Persetujuan Awal',
            ]);

            // Ganti dokumen hanya jika pengguna mengunggah file baru.
            foreach ($kategoriDokumen as $field => $kategori) {
                if (!$request->hasFile($field)) {
                    continue;
                }

                $file = $request->file($field);

                $path = $file->store(
                    'dokumen-pengajuan',
                    'public'
                );

                $fileTersimpan[] = $path;

                $dokumenSebelumnya = $dokumenLama->get($kategori);

                if ($dokumenSebelumnya) {
                    $fileLamaUntukDihapus[] = $dokumenSebelumnya->File_path;

                    $dokumenSebelumnya->update([
                        'File_path' => $path,
                        'Tanggal_dokumen' => now()->toDateString(),
                    ]);
                } else {
                    $pengajuan->dokumen()->create([
                        'Id_pengadaan' => null,
                        'Tanggal_dokumen' => now()->toDateString(),
                        'File_path' => $path,
                        'Kategori_dokumen' => $kategori,
                    ]);
                }
            }
        });
    } catch (\Throwable $e) {
        // Hapus file baru jika transaksi database gagal.
        if (!empty($fileTersimpan)) {
            Storage::disk('public')->delete($fileTersimpan);
        }

        throw $e;
    }

    // File lama dihapus setelah transaksi berhasil.
    if (!empty($fileLamaUntukDihapus)) {
        Storage::disk('public')->delete($fileLamaUntukDihapus);
    }

    return redirect()
        ->route('unit.permohonan.index')
        ->with(
            'success',
            $request->aksi === 'draf'
                ? 'Draf permohonan berhasil diperbarui.'
                : 'Permohonan berhasil dikirim dan menunggu persetujuan awal.'
        );
}
}