<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengadaan extends Model
{
    protected $table = 'pengadaan';

    protected $primaryKey = 'Id_pengadaan';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'Id_PPK_assigned',
        'Id_pengajuan',
        'Id_MAK',
        'Jenis_pengadaan',
        'Metode_pengadaan',
        'Nominal_anggaran',
        'Status_pengadaan',
    ];

    protected $casts = [
        'Nominal_anggaran' => 'decimal:2',
    ];

    // Relasi ke pengajuan surat.
    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(
            PengajuanSurat::class,
            'Id_pengajuan',
            'Id_pengajuan'
        );
    }

    // Relasi ke MAK.
    public function mak(): BelongsTo
    {
        return $this->belongsTo(
            Mak::class,
            'Id_MAK',
            'Id_MAK'
        );
    }

    // Relasi ke penugasan PP.
    public function penugasanPP(): HasOne
    {
        return $this->hasOne(
            PenugasanPP::class,
            'Id_pengadaan',
            'Id_pengadaan'
        );
    }

    // Relasi ke penugasan PPK.
    public function penugasanPPK(): HasOne
    {
        return $this->hasOne(
            PenugasanPPK::class,
            'Id_pengadaan',
            'Id_pengadaan'
        );
    }

    // Relasi ke seluruh pembayaran.
    public function pembayaran(): HasMany
    {
        return $this->hasMany(
            Pembayaran::class,
            'Id_pengadaan',
            'Id_pengadaan'
        );
    }
}
