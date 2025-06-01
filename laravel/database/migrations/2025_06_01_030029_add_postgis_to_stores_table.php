<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint; // Blueprint standar Laravel
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) { // Gunakan Blueprint standar Laravel
            // Hapus kolom latitude dan longitude lama jika ada
            if (Schema::hasColumn('stores', 'latitude') && Schema::hasColumn('stores', 'longitude')) {
                $table->dropColumn(['latitude', 'longitude']);
            }

            // Tambahkan kolom PostGIS geography untuk lokasi toko
            // 'GEOGRAPHY' sebagai argumen ketiga untuk postgisType
            $table->magellanPoint('location', 4326, 'GEOGRAPHY')->nullable()->after('address');
        });

        // Tambahkan spatial index
        DB::statement('CREATE INDEX IF NOT EXISTS stores_location_gix ON stores USING GIST (location);');
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) { // Gunakan Blueprint standar Laravel
            DB::statement('DROP INDEX IF EXISTS stores_location_gix;');
            if (Schema::hasColumn('stores', 'location')) {
                $table->dropColumn('location');
            }
            // Opsional: Tambahkan kembali kolom latitude/longitude lama jika rollback
            // $table->decimal('latitude', 10, 6)->nullable();
            // $table->decimal('longitude', 10, 6)->nullable();
        });
    }
};
