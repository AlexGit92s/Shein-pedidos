<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Crea/actualiza la única cuenta de administradora desde ADMIN_EMAIL y ADMIN_PASSWORD.
    public function run(): void
    {
        $email = config('app.admin_email');
        $password = config('app.admin_password');

        if (! $email || ! $password) {
            $this->command->error('Define ADMIN_EMAIL y ADMIN_PASSWORD.');

            return;
        }

        User::updateOrCreate(['email' => $email], ['name' => 'Admin', 'password' => $password]);
    }
}
