<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'news';

    public static function boot()
    {
        parent::boot();

        // Membuat slug yang menggabungkan tanggal dan judul
        static::creating(function ($news) {
            $news->slug = date('Y-m-d') . '-' . Str::slug($news->title);
        });
    }
}
