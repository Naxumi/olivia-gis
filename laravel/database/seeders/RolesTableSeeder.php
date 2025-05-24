<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role; // Pastikan namespace model Role Anda benar

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar peran yang ingin Anda tambahkan
        // Sesuaikan dengan enum yang ada di migrasi Anda
        $roles = [
            ['name' => 'buyer'],
            ['name' => 'seller'],
            ['name' => 'partner'],
            ['name' => 'distributor'],
            ['name' => 'admin'],
        ];

        // Loop melalui array roles dan buat record baru jika belum ada
        foreach ($roles as $roleData) {
            Role::firstOrCreate(['name' => $roleData['name']]);
            // Menggunakan firstOrCreate() akan membuat role jika belum ada berdasarkan 'name',
            // atau mengambil yang sudah ada. Ini aman jika seeder dijalankan berkali-kali.
            // Karena ada unique constraint pada 'name', Role::create($roleData) juga bisa
            // jika Anda yakin seeder hanya dijalankan sekali pada tabel kosong.
        }

        // Anda juga bisa menambahkan output ke console jika ingin
        // $this->command->info('Tabel roles berhasil di-seed!');
    }
}