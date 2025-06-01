<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistics', function (Blueprint $table) { // Gunakan Blueprint standar Laravel

            // Hapus kolom current_latitude & current_longitude lama jika ada
            if (Schema::hasColumn('logistics', 'current_latitude') && Schema::hasColumn('logistics', 'current_longitude')) {
                $table->dropColumn(['current_latitude', 'current_longitude']);
            }
            $table->magellanPoint('current_location', 4326, 'GEOGRAPHY')->nullable();
            // $table->timestamps(); // Jika Anda ingin created_at & updated_at standar Laravel
        });
        DB::statement('CREATE INDEX IF NOT EXISTS logistics_current_location_gix ON logistics USING GIST (current_location);');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS logistics_current_location_gix;'); // Hapus index dulu
        Schema::dropIfExists('logistics');
    }
};
