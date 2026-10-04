<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! User::sincronizarAdmin()) {
            $this->command->error('Define ADMIN_EMAIL y ADMIN_PASSWORD.');
        }
    }
}
