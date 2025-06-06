<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- Pastikan DB Facade di-import

return new class extends Migration
{
    /**
     * Menjalankan migrasi.
     */
    public function up(): void
    {
        Schema::dropIfExists('reviews');

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->unique()->constrained('transactions')->onDelete('cascade');
            $table->unsignedSmallInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // Tambahkan CHECK CONSTRAINT menggunakan query mentah setelah tabel dibuat
        DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_rating_check CHECK (rating >= 1 AND rating <= 5)');
    }

    /**
     * Membatalkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
