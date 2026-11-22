<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mak extends Model
{
    protected $table = 'mak';

    protected $primaryKey = 'Id_MAK';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'Kode_MAK',
        'Nama_MAK',
        'Jenis_anggaran',
    ];

    // Satu MAK dapat digunakan oleh banyak pengadaan.
    public function pengadaan(): HasMany
    {
        return $this->hasMany(
            Pengadaan::class,
            'Id_MAK',
            'Id_MAK'
        );
    }
}
