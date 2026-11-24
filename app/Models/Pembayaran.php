<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $primaryKey = 'Id_pembayaran';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'Id_pengadaan',
        'Id_user_keuangan',
        'Tanggal_bayar',
        'Status_bayar',
        'Nominal_dibayar',
        'Bruto',
        'Neto',
        'Termin_ke',
    ];

    protected $casts = [
        'Tanggal_bayar' => 'date',
        'Nominal_dibayar' => 'decimal:2',
        'Termin_ke' => 'integer',
    ];

    // Relasi ke pengadaan.
    public function pengadaan(): BelongsTo
    {
        return $this->belongsTo(
            Pengadaan::class,
            'Id_pengadaan',
            'Id_pengadaan'
        );
    }

    // Relasi ke user bagian keuangan.
    public function petugasKeuangan(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'Id_user_keuangan',
            'Id_user'
        );
    }

    // Relasi ke dokumen pembayaran.
    public function dokumen(): HasMany
    {
        return $this->hasMany(
            DokumenFile::class,
            'Id_pembayaran',
            'Id_pembayaran'
        );
    }
}
