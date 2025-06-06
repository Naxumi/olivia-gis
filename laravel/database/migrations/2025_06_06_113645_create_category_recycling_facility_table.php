<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_recycling_facility', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('recycling_facility_id')->constrained('recycling_facilities')->onDelete('cascade');
            $table->primary(['category_id', 'recycling_facility_id']); // Primary key gabungan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_recycling_facility');
    }
};
