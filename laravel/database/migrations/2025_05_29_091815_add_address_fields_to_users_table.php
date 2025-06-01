<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom alamat, sesuaikan dengan kebutuhan detail Anda
            // Tambahkan setelah kolom yang sudah ada, misalnya 'eco_points'
            $table->string('phone_number')->after('eco_points')->nullable();
            $table->text('address_detail')->after('phone_number')->nullable(); // Untuk detail jalan, nomor rumah, RT/RW
            $table->string('village')->after('address_detail')->nullable();    // Kelurahan / Desa
            $table->string('subdistrict')->after('village')->nullable();      // Kecamatan
            $table->string('city_regency')->after('subdistrict')->nullable();  // Kota / Kabupaten
            $table->string('province')->after('city_regency')->nullable();
            $table->string('postal_code', 10)->after('province')->nullable();
            $table->decimal('latitude', 10, 7)->after('postal_code')->nullable();
            $table->decimal('longitude', 10, 7)->after('latitude')->nullable();
            $table->text('address_notes')->after('longitude')->nullable(); // Catatan tambahan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'address_detail',
                'village',
                'subdistrict',
                'city_regency',
                'province',
                'postal_code',
                'latitude',
                'longitude',
                'address_notes'
            ]);
        });
    }
};
