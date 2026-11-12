<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'Id_role';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'Nama_role',
        'Hak_akses',
    ];

    // Relasi: Satu Role memiliki banyak User
    public function users(): HasMany
    {
        return $this->hasMany(
            User::class,
            'Id_role',
            'Id_role'
        );
    }
}
