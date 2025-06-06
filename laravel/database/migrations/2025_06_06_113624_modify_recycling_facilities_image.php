<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Menjalankan migrasi untuk mengubah tabel.
     */
    public function up(): void
    {
        Schema::table('recycling_facilities', function (Blueprint $table) {
            // Langkah 1: Tambahkan kolom baru. Dibuat nullable dulu agar tidak error pada baris data yang sudah ada.
            $table->string('image_path')->nullable()->after('address');
        });

        // Langkah 2: Tambahkan spatial index pada kolom baru untuk performa query.
        DB::statement('CREATE INDEX IF NOT EXISTS recycling_facilities_location_gix ON recycling_facilities USING GIST (location);');
    }

    /**
     * Membatalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::table('recycling_facilities', function (Blueprint $table) {
            // Langkah 1 (kebalikan dari up): Tambahkan kembali kolom lama, buat nullable.
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
        });

        // Langkah 2: Kembalikan data dari 'location' ke kolom lat/long lama.
        DB::statement('UPDATE recycling_facilities SET latitude = ST_Y(location::geometry), longitude = ST_X(location::geometry) WHERE location IS NOT NULL;');

        // Langkah 3: Hapus index spasial dan kolom-kolom baru.
        DB::statement('DROP INDEX IF EXISTS recycling_facilities_location_gix;');
        Schema::table('recycling_facilities', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'location']);
        });
    }
};