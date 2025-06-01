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
        Schema::table('transactions', function (Blueprint $table) {
            // Pastikan nama kolom ini unik dan tidak bentrok dengan kolom lain
            // Nama ini harus sama dengan yang akan Anda gunakan di $fillable Transaction model
            // dan saat create di TransactionController
            if (!Schema::hasColumn('transactions', 'delivery_recipient_name')) {
                $table->string('delivery_recipient_name')->after('payment_method')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'delivery_phone_number')) {
                $table->string('delivery_phone_number')->after('delivery_recipient_name')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'delivery_address_detail')) {
                $table->text('delivery_address_detail')->after('delivery_phone_number')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'delivery_village')) {
                $table->string('delivery_village')->after('delivery_address_detail')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'delivery_subdistrict')) {
                $table->string('delivery_subdistrict')->after('delivery_village')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'delivery_city_regency')) {
                $table->string('delivery_city_regency')->after('delivery_subdistrict')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'delivery_province')) {
                $table->string('delivery_province')->after('delivery_city_regency')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'delivery_postal_code')) {
                $table->string('delivery_postal_code', 10)->after('delivery_province')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'delivery_latitude')) {
                $table->decimal('delivery_latitude', 10, 7)->after('delivery_postal_code')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'delivery_longitude')) {
                $table->decimal('delivery_longitude', 10, 7)->after('delivery_latitude')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'delivery_notes')) {
                $table->text('delivery_notes')->after('delivery_longitude')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_recipient_name',
                'delivery_phone_number',
                'delivery_address_detail',
                'delivery_village',
                'delivery_subdistrict',
                'delivery_city_regency',
                'delivery_province',
                'delivery_postal_code',
                'delivery_latitude',
                'delivery_longitude',
                'delivery_notes'
            ]);
        });
    }
};
