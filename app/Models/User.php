<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\ValidationException;
use RuntimeException;

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
        'NIP',
        'Status_user',
    ];
    protected $hidden = [
        'Password',
        'Password_fingerprint',
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

    /**
     * Menetapkan password sekaligus membuat fingerprint unik.
     */
    public function setPasswordWithFingerprint(string $plainPassword): void
    {
        $key = config('app.password_fingerprint_key');

        if (empty($key)) {
            throw new RuntimeException(
                'PASSWORD_FINGERPRINT_KEY belum dikonfigurasi.'
            );
        }

        // Membuat fingerprint dari password asli.
        $fingerprint = hash_hmac('sha256', $plainPassword, $key);

        // Memeriksa apakah password sudah digunakan akun lain.
        $query = static::where('Password_fingerprint', $fingerprint);

        if ($this->exists) {
            $query->whereKeyNot($this->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'password' => 'Password sudah digunakan oleh akun lain. Silakan gunakan password berbeda.',
            ]);
        }

        // Laravel akan meng-hash password melalui hashed cast.
        $this->Password = $plainPassword;

        // Fingerprint disimpan terpisah dari password hash.
        $this->Password_fingerprint = $fingerprint;
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
