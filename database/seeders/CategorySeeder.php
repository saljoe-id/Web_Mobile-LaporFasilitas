<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Jalan & Jembatan', 'slug' => 'jalan-jembatan'],
            ['name' => 'Penerangan Jalan', 'slug' => 'penerangan-jalan'],
            ['name' => 'Drainase & Saluran Air', 'slug' => 'drainase-saluran-air'],
            ['name' => 'Taman & Ruang Publik', 'slug' => 'taman-ruang-publik'],
            ['name' => 'Fasilitas Kesehatan', 'slug' => 'fasilitas-kesehatan'],
            ['name' => 'Fasilitas Pendidikan', 'slug' => 'fasilitas-pendidikan'],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [...$category, 'is_active' => true],
            );
        }
    }
}
