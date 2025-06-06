<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_images', function (Blueprint $table) {
            $table->id();
            // Foreign key yang terhubung ke tabel wastes
            $table->foreignId('waste_id')->constrained('wastes')->onDelete('cascade');
            $table->string('path'); // Untuk menyimpan path file gambar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_images');
    }
};
