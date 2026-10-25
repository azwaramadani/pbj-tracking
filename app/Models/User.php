<?php


namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'Username',
    'Password',
    'Nama_lengkap',
    'Id_role',
    'Asal_unit_bagian',
])]
#[Hidden(['Password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'Id_user';

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
}
