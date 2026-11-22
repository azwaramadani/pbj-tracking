<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
