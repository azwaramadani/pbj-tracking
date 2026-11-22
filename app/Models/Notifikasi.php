<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $primaryKey = 'Id_notifikasi';

    protected $fillable = [
        'Id_user',
        'Id_pengajuan',
        'Id_pengadaan',
        'Kategori',
        'Judul',
        'Pesan',
        'Icon',
        'Dibaca_pada',
    ];

    protected $casts = [
        'Dibaca_pada' => 'datetime',
    ];

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'Id_user',
            'Id_user'
        );
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(
            PengajuanSurat::class,
            'Id_pengajuan',
            'Id_pengajuan'
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
