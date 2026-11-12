<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'Id_user';

    protected $keyType = 'int';

    public $incrementing = true;

    protected $fillable = [
        'Email_user',
        'Password',
        'Nama_lengkap',
        'Id_role',
        'Asal_unit',
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    public function getAuthPasswordName(): string
    {
        return 'Password';
    }

    protected function casts(): array
    {
        return [
            'Password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,
            'Id_role',
            'Id_role'
        );
    }
}
