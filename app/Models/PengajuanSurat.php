<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanSurat extends Model
{
    protected $table = 'pengajuan_surat';

    protected $primaryKey = 'Id_pengajuan';

    public $timestamps = false;

    protected $fillable = [
        'Id_user_pemohon',
        'Tujuan_pengadaan',
        'Status_surat',
        'Asal_surat',
        'Tanggal_surat',
        'Judul_surat',
        'No_surat',
        'Nominal_pengadaan',
    ];

    protected $casts = [
        'Tanggal_surat' => 'date',
    ];

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'Id_user_pemohon',
            'Id_user'
        );
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(
            DokumenFile::class,
            'Id_pengajuan',
            'Id_pengajuan'
        );
    }
}
