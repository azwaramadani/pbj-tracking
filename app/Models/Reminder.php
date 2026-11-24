<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    protected $table = 'reminder';

    protected $primaryKey = 'Id_reminder';

    public $timestamps = false;

    protected $fillable = [
        'Id_pengadaan',
        'Jenis_termin',
        'Status_reminder',
        'Tanggal_pengiriman',
        'Tanggal_alarm_H2',
        'Termin_ke',
    ];

    protected $casts = [
        'Tanggal_pengiriman' => 'date',
        'Tanggal_alarm_H2' => 'date',
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