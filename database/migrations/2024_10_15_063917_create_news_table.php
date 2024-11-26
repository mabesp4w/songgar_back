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
        Schema::create('news', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title'); // Judul berita
            $table->string('slug')->unique(); // Slug berita (unik)
            $table->string('author')->nullable(); // Penulis berita
            $table->string('img_news'); // Gambar berita
            $table->date('news_date');
            $table->text('content'); // Isi berita
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
