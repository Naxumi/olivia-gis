<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Panggil RolesTableSeeder di sini
        $this->call([
            RolesTableSeeder::class,
            // Anda bisa menambahkan seeder lain di sini jika ada
            CategoriesTableSeeder::class,
        ]);
    }
}
