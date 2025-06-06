<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint; // Blueprint standar Laravel
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recycling_facilities', function (Blueprint $table) {
            // Hapus kolom latitude dan longitude lama jika ada
            if (Schema::hasColumn('recycling_facilities', 'latitude') && Schema::hasColumn('recycling_facilities', 'longitude')) {
                $table->dropColumn(['latitude', 'longitude']);
            }

            // Tambahkan kolom PostGIS geography untuk lokasi fasilitas
            // Menggunakan helper dari clickbar/laravel-magellan. Dibuat NOT NULL sesuai DDL awal Anda.
            $table->magellanPoint('location', 4326, 'GEOGRAPHY')->after('address');
        });

        // Tambahkan spatial index untuk performa query geospasial
        DB::statement('CREATE INDEX IF NOT EXISTS recycling_facilities_location_gix ON recycling_facilities USING GIST (location);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recycling_facilities', function (Blueprint $table) {
            DB::statement('DROP INDEX IF EXISTS recycling_facilities_location_gix;');
            if (Schema::hasColumn('recycling_facilities', 'location')) {
                $table->dropColumn('location');
            }
            // Kembalikan kolom lama jika rollback
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
        });
    }
};