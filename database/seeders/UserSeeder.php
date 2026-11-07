<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@pengaduan.test'],
            [
                'name' => 'Administrator',
                'password' => 'password',
                'role' => UserRole::Admin,
                'is_active' => true,
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'petugas@pengaduan.test'],
            [
                'name' => 'Petugas Lapangan',
                'password' => 'password',
                'role' => UserRole::Petugas,
                'is_active' => true,
            ],
        );
    }
}
