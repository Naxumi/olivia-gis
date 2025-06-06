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
        // Tambahkan pengecekan di sini:
        // Cek apakah tabel 'personal_access_tokens' BELUM ada.
        if (!Schema::hasTable('personal_access_tokens')) {
            // Jika tabel belum ada, baru jalankan perintah untuk membuatnya.
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
        // Jika tabel sudah ada, method up() ini tidak akan melakukan apa-apa.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Method down() Anda sudah aman karena menggunakan dropIfExists.
        // Ini hanya akan menghapus tabel jika tabelnya memang ada.
        Schema::dropIfExists('personal_access_tokens');
    }
};