<?php

namespace App\Http\Controllers\Wadir2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected string $prefixRute = 'wadir2';

    private const FILTER_STATUS = [
        'menunggu',
        'diproses',
        'selesai',
        'revisi',
        'ditolak',
    ];

    private const PER_HALAMAN = 6;

    /**
     * Mengubah status database menjadi status untuk tampilan dashboard.
     */
    private function mapStatus(?string $status): ?string
    {
        $status = strtolower(trim((string) $status));

        if ($status === 'draft' || $status === '') {
            return null;
        }

        if (str_contains($status, 'menunggu')) {
            return 'menunggu';
        }

        if (str_contains($status, 'revisi')) {
            return 'revisi';
        }

        if (str_contains($status, 'tolak')) {
            return 'ditolak';
        }

        if (str_contains($status, 'selesai')) {
            return 'selesai';
        }

        if (
            str_contains($status, 'proses') ||
            str_contains($status, 'setuju')
        ) {
            return 'diproses';
        }

        return 'diproses';
    }

    /**
     * Dashboard utama Wadir 2 menggunakan data PostgreSQL.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $status = (string) $request->query('status', '');

        $status = in_array($status, self::FILTER_STATUS, true)
            ? $status
            : '';

        // Mengambil status pengajuan untuk menghitung ringkasan.
        $semuaStatus = DB::table('pengajuan_surat')
            ->pluck('Status_surat');

        $jumlahMenunggu = 0;
        $jumlahSelesai = 0;
        $jumlahRevisi = 0;
        $jumlahDitolak = 0;

        foreach ($semuaStatus as $statusDatabase) {
            $statusDashboard = $this->mapStatus($statusDatabase);

            if ($statusDashboard === 'menunggu') {
                $jumlahMenunggu++;
            } elseif ($statusDashboard === 'selesai') {
                $jumlahSelesai++;
            } elseif ($statusDashboard === 'revisi') {
                $jumlahRevisi++;
            } elseif ($statusDashboard === 'ditolak') {
                $jumlahDitolak++;
            }
        }

        // Query pengajuan yang sudah dikirim, bukan Draft.
        $query = DB::table('pengajuan_surat as p')
            ->leftJoin('users as u', 'u.Id_user', '=', 'p.Id_user_pemohon')
            ->whereRaw('LOWER(p."Status_surat") <> ?', ['draft'])
            ->select([
                'p.Id_pengajuan',
                'p.No_surat',
                'p.Tanggal_surat',
                'p.Asal_surat',
                'p.Judul_surat',
                'p.Status_surat',
                'u.Nama_lengkap',
            ]);

        // Pencarian berdasarkan nomor surat.
        if ($q !== '') {
            $query->where('p.No_surat', 'ILIKE', "%{$q}%");
        }

        // Filter berdasarkan status dashboard.
        if ($status !== '') {
            if ($status === 'menunggu') {
                $query->whereRaw(
                    'LOWER(p."Status_surat") LIKE ?',
                    ['%menunggu%']
                );
            } elseif ($status === 'selesai') {
                $query->whereRaw(
                    'LOWER(p."Status_surat") LIKE ?',
                    ['%selesai%']
                );
            } elseif ($status === 'revisi') {
                $query->whereRaw(
                    'LOWER(p."Status_surat") LIKE ?',
                    ['%revisi%']
                );
            } elseif ($status === 'ditolak') {
                $query->whereRaw(
                    'LOWER(p."Status_surat") LIKE ?',
                    ['%tolak%']
                );
            } elseif ($status === 'diproses') {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->whereRaw(
                            'LOWER(p."Status_surat") LIKE ?',
                            ['%proses%']
                        )
                        ->orWhereRaw(
                            'LOWER(p."Status_surat") LIKE ?',
                            ['%setuju%']
                        );
                });
            }
        }

        // Pagination database.
        $paginator = $query
            ->orderByDesc('p.Id_pengajuan')
            ->paginate(self::PER_HALAMAN)
            ->withQueryString();

        // Menyesuaikan bentuk data dengan kebutuhan Blade.
        $baris = $paginator->getCollection()->map(function ($item) {
            return [
                'id' => $item->Id_pengajuan,
                'no_surat' => $item->No_surat,
                'tgl_surat' => $item->Tanggal_surat,
                'unit' => $item->Asal_surat ?: '-',
                'judul' => $item->Judul_surat,
                'status' => $this->mapStatus($item->Status_surat),
                'tgl_kirim' => $item->Tanggal_surat,
            ];
        });

        return view('wadir2.dashboard', [
            'prefixRute' => $this->prefixRute,

            'ringkasan' => [
                [
                    'jumlah' => $jumlahMenunggu,
                    'label' => "Menunggu\nProses",
                ],
                [
                    'jumlah' => $jumlahSelesai,
                    'label' => "Permohonan\nSelesai",
                ],
                [
                    'jumlah' => $jumlahRevisi,
                    'label' => "Perlu\nDirevisi",
                ],
                [
                    'jumlah' => $jumlahDitolak,
                    'label' => "Permohonan\nDitolak",
                ],
            ],

            'pengajuan' => $baris,

            'totalPengajuan' => $paginator->total(),

            'paginasi' => [
                'halaman' => $paginator->currentPage(),
                'total_halaman' => max(1, $paginator->lastPage()),
                'dari' => $paginator->firstItem() ?? 0,
                'sampai' => $paginator->lastItem() ?? 0,
                'total' => $paginator->total(),
            ],

            'filter' => [
                'q' => $q,
                'status' => $status,
            ],
        ]);
    }
}
