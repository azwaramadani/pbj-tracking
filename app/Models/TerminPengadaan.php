<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TerminPengadaan extends Model
{
    protected $table = 'termin_pengadaan';

    protected $primaryKey = 'Id_termin_pengadaan';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'Id_pengadaan',
        'Termin_ke',
        'Nominal_termin',
    ];

    protected $casts = [
        'Termin_ke' => 'integer',
        'Nominal_termin' => 'decimal:2',
    ];

    public function pengadaan(): BelongsTo
    {
        return $this->belongsTo(
            Pengadaan::class,
            'Id_pengadaan',
            'Id_pengadaan'
        );
    }
}