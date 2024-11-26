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
        Schema::create('facilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nm_facility'); // Nama sarana prasarana
            $table->enum('type', ['Sarana', 'Prasarana']);
            $table->string('location')->nullable(); // Lokasi (misalnya gedung A, lantai 2)
            $table->enum('condition', ['Baik', 'Rusak', 'Sedang Diperbaiki']);
            $table->unsignedInteger('quantity')->default(1); // Kapasitas fasilitas (misalnya jumlah kursi di kelas)
            $table->text('description')->nullable(); // Deskripsi fasilitas
            $table->string('img_facility')->nullable(); // Lokasi foto fasilitas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
