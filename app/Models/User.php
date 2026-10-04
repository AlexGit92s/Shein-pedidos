<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // La cuenta de la administradora sale de ADMIN_EMAIL / ADMIN_PASSWORD.
    // Se llama al intentar entrar, así no depende de que el deploy corra el seeder.
    public static function sincronizarAdmin(): bool
    {
        $email = config('app.admin_email');
        $password = config('app.admin_password');

        if (! $email || ! $password) {
            return false;
        }

        $admin = self::firstOrNew(['email' => $email]);
        if (! $admin->exists || ! Hash::check($password, $admin->password)) {
            $admin->fill(['name' => 'Admin', 'password' => $password])->save();
        }

        return true;
    }
}
