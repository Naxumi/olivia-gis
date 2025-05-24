<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category; // Pastikan namespace model Role Anda benar

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar peran yang ingin Anda tambahkan
        // Sesuaikan dengan enum yang ada di migrasi Anda
        $categories = [
            ['name' => 'plastik'],
            ['name' => 'kertas'],
            ['name' => 'logam'],
            ['name' => 'kaca'],
            ['name' => 'elektronik'],
            ['name' => 'baterai'],
            ['name' => 'organik'],
            ['name' => 'anorganik'],
            ['name' => 'campuran'],
        ];

        // Loop melalui array categories dan buat record baru jika belum ada
        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']]);
            // Menggunakan firstOrCreate() akan membuat role jika belum ada berdasarkan 'name',
            // atau mengambil yang sudah ada. Ini aman jika seeder dijalankan berkali-kali.
            // Karena ada unique constraint pada 'name', Role::create($roleData) juga bisa
            // jika Anda yakin seeder hanya dijalankan sekali pada tabel kosong.
        }

        // Anda juga bisa menambahkan output ke console jika ingin
        // $this->command->info('Tabel roles berhasil di-seed!');
    }
}
