<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'Nama_lengkap' => fake()->name(),
            'Email_user' => fake()->unique()->safeEmail(),
            'Id_role' => null,
            'Asal_unit' => null,
        ];
    }

    /**
     * Membuat password dan fingerprint unik untuk setiap akun.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (User $user) {
            $user->setPasswordWithFingerprint(
                Str::random(40)
            );
        });
    }
}
