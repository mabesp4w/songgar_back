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
        Schema::create('announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('major_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Judul pengumuman
            $table->text('content'); // Isi pengumuman
            $table->string('slug')->unique(); // Slug pengumuman (unik)
            $table->date('announcement_date'); // Tanggal pengumuman
            $table->string('author')->nullable(); // Penulis atau pengunggah pengumuman
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
