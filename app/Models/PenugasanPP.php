<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenugasanPP extends Model
{
    protected $table = 'penugasan_pp';

    protected $primaryKey = 'Id_penugasan_pp';

    public $timestamps = false;

    protected $fillable = [
        'Id_pengadaan',
        'Id_user_pp',
    ];

    public function pengadaan(): BelongsTo
    {
        return $this->belongsTo(
            Pengadaan::class,
            'Id_pengadaan',
            'Id_pengadaan'
        );
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'Id_user_pp',
            'Id_user'
        );
    }
}
