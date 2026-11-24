<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenFile extends Model
{
    protected $table = 'dokumen_file';

    protected $primaryKey = 'Id_dokumen';

    public $timestamps = false;

    protected $fillable = [
        'Id_pengadaan',
        'Id_pengajuan',
        'Id_pembayaran',
        'Tanggal_dokumen',
        'File_path',
        'Kategori_dokumen',
    ];

    protected $casts = [
        'Tanggal_dokumen' => 'date',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(
            PengajuanSurat::class,
            'Id_pengajuan',
            'Id_pengajuan'
        );
    }
    public function pembayaran(): BelongsTo
{
    return $this->belongsTo(
        Pembayaran::class,
        'Id_pembayaran',
        'Id_pembayaran'
    );
}
public function pengadaan(): BelongsTo
{
    return $this->belongsTo(
        Pengadaan::class,
        'Id_pengadaan',
        'Id_pengadaan'
    );
}
}
