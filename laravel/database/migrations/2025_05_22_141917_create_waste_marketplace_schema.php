<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('eco_points')->default(0);
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['buyer', 'seller', 'partner', 'distributor', 'admin'])->unique();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('eco_point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('source_type', ['transaction', 'review', 'referral']);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->integer('points');
            $table->enum('points_type', ['plus', 'minus']);
            $table->text('description');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('address');
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            // Tambahkan timestamps jika diperlukan
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // Tambahkan timestamps jika diperlukan
            $table->timestamps();
        });

        Schema::create('wastes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->integer('stock');
            $table->enum('status', ['available', 'sold', 'expired']);
            $table->decimal('price', 10, 2);
            $table->text('description');
            $table->integer('sold_count')->default(0);
            // Tambahkan timestamps jika diperlukan
            $table->timestamps();
        });

        Schema::create('waste_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_id')->constrained('wastes')->onDelete('cascade');
            $table->integer('volume_in_grams');
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            // Tambahkan timestamps jika diperlukan
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('waste_variant_id')->constrained('waste_variants')->onDelete('cascade');
            $table->tinyInteger('rating'); // Pastikan tipe data sesuai, 1-5
            $table->text('comment');
            $table->timestamp('created_at')->useCurrent();
            // Tambahkan updated_at jika diperlukan
            // $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
        });

        // 1. Buat tabel transactions, definisikan kolom logistics_id tapi JANGAN buat constraint dulu
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users');
            $table->foreignId('buyer_id')->constrained('users');
            $table->foreignId('waste_variant_id')->constrained('waste_variants');
            $table->foreignId('store_id')->constrained('stores');
            $table->unsignedBigInteger('logistics_id')->nullable(); // Definisikan kolom saja
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'picked_up', 'delivered', 'cancelled']);
            $table->integer('eco_points_earned')->default(0);
            $table->string('payment_method');
            $table->timestamps();
            $table->timestamp('completed_at')->nullable();
        });

        // 2. Buat tabel logistics, ini bisa langsung membuat constraint ke transactions
        Schema::create('logistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade'); // Tambahkan onDelete jika perlu
            $table->foreignId('distributor_id')->constrained('users');
            $table->enum('status', ['scheduled', 'in_transit', 'delivered', 'cancelled']);
            $table->decimal('distance_km', 8, 2)->nullable(); // Jadikan nullable jika bisa kosong di awal
            $table->integer('duration_minutes')->nullable(); // Jadikan nullable jika bisa kosong di awal
            $table->timestamp('estimated_delivery_time')->nullable(); // Jadikan nullable jika bisa kosong di awal
            $table->decimal('current_latitude', 10, 6)->nullable();
            $table->decimal('current_longitude', 10, 6)->nullable();
            $table->timestamp('last_updated_at')->useCurrent();
            // Tambahkan timestamps() jika created_at juga dibutuhkan selain last_updated_at
        });

        // 3. SEKARANG, tambahkan foreign key constraint dari transactions ke logistics
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('logistics_id')
                ->references('id')
                ->on('logistics')
                ->onDelete('set null'); // atau onDelete('cascade') sesuai kebutuhan
        });

        Schema::create('distributor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Tambahkan onDelete jika perlu
            $table->string('contact_person');
            $table->string('phone_number');
            // Tambahkan timestamps jika diperlukan
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade'); // Tambahkan onDelete jika perlu
            $table->string('blockchain_tx_id');
            $table->string('certificate_url');
            $table->decimal('carbon_offset_kg', 10, 2);
            $table->timestamp('issued_at');
            // Tambahkan timestamps (created_at, updated_at) jika diperlukan
            $table->timestamps();
        });

        Schema::create('recycling_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['rvm', 'waste_bank']);
            $table->text('address');
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->string('operational_hours');
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->foreignId('owner_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('facility_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('recycling_facilities')->onDelete('cascade');
            $table->foreignId('waste_category_id')->constrained('categories');
            $table->text('description')->nullable();
            // Tambahkan timestamps jika diperlukan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Urutan drop sebaiknya dibalik dan menangani foreign key constraints
        // atau pastikan semua tabel yang merujuk sudah di-drop terlebih dahulu.
        // Untuk mengatasi circular dependency saat drop, kita bisa nonaktifkan foreign key check sementara
        // Namun, dengan Laravel, urutan dropIfExist yang benar biasanya sudah cukup.
        // Jika ada masalah saat drop, kita mungkin perlu memodifikasi `Schema::table` untuk drop foreign key dulu.

        Schema::disableForeignKeyConstraints(); // Nonaktifkan FK check sementara (hati-hati saat production)

        Schema::dropIfExists('facility_collections');
        Schema::dropIfExists('recycling_facilities');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('distributor_profiles');
        // Hapus foreign key dari transactions ke logistics sebelum drop logistics jika ada masalah
        // Schema::table('transactions', function (Blueprint $table) {
        //     if (Schema::hasColumn('transactions', 'logistics_id')) { // Cek jika kolom ada
        //         // Cek nama constraint spesifik jika perlu, atau Laravel akan coba cari berdasarkan konvensi
        //         // $sm = Schema::getConnection()->getDoctrineSchemaManager();
        //         // $foreignKeys = $sm->listTableForeignKeys('transactions');
        //         // foreach ($foreignKeys as $foreignKey) {
        //         //     if ($foreignKey->getForeignTableName() == 'logistics' && in_array('logistics_id', $foreignKey->getLocalColumns())) {
        //         //         $table->dropForeign($foreignKey->getName());
        //         //     }
        //         // }
        //         // Atau coba drop dengan konvensi nama Laravel:
        //         // $table->dropForeign(['logistics_id']); // Ini mungkin perlu nama constraint explisit
        //     }
        // });
        Schema::dropIfExists('logistics');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('waste_variants');
        Schema::dropIfExists('wastes');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('eco_point_logs');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');

        Schema::enableForeignKeyConstraints(); // Aktifkan kembali FK check
    }
};
