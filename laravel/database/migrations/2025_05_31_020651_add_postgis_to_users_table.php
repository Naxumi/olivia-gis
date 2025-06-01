<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Clickbar\Magellan\Schema\MagellanSchema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) { // Gunakan Blueprint standar Laravel
            // Hapus kolom lat/long lama jika ada untuk menghindari duplikasi
            if (Schema::hasColumn('users', 'latitude') && Schema::hasColumn('users', 'longitude')) {
                $table->dropColumn(['latitude', 'longitude']);
            }

            // Tambahkan kolom PostGIS geography untuk lokasi user
            // Menggunakan helper dari clickbar/laravel-magellan
            // Parameter ketiga 'GEOGRAPHY' adalah postgisType
            $table->magellanPoint('location', 4326, 'GEOGRAPHY')->nullable()->after('postal_code');
        });

        // Tambahkan spatial index untuk performa query yang lebih baik
        DB::statement('CREATE INDEX IF NOT EXISTS users_location_gix ON users USING GIST (location);');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) { // Gunakan Blueprint standar Laravel
            DB::statement('DROP INDEX IF EXISTS users_location_gix;');
            if (Schema::hasColumn('users', 'location')) {
                $table->dropColumn('location');
            }
            // Opsional: Tambahkan kembali kolom latitude/longitude lama jika melakukan rollback penuh
            // $table->decimal('latitude', 10, 7)->nullable();
            // $table->decimal('longitude', 10, 7)->nullable();
        });
    }
};
