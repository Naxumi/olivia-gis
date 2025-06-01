<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) { // Gunakan Blueprint standar Laravel
            // Hapus kolom delivery_latitude & delivery_longitude lama jika ada
            if (Schema::hasColumn('transactions', 'delivery_latitude') && Schema::hasColumn('transactions', 'delivery_longitude')) {
                $table->dropColumn(['delivery_latitude', 'delivery_longitude']);
            }
            // Tambahkan kolom PostGIS untuk lokasi pengiriman snapshot
            $table->magellanPoint('delivery_location', 4326, 'GEOGRAPHY')->nullable()->after('delivery_postal_code');
        });
        DB::statement('CREATE INDEX IF NOT EXISTS transactions_delivery_location_gix ON transactions USING GIST (delivery_location);');
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) { // Gunakan Blueprint standar Laravel
            DB::statement('DROP INDEX IF EXISTS transactions_delivery_location_gix;');
            if (Schema::hasColumn('transactions', 'delivery_location')) {
                $table->dropColumn('delivery_location');
            }
        });
    }
};
