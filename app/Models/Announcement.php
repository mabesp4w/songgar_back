<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Announcement extends Model
{
    use HasFactory, HasUuids;

    public static function boot()
    {
        parent::boot();

        // Membuat slug yang menggabungkan tanggal dan judul
        static::creating(function ($announcement) {
            $announcement->slug = date('Y-m-d') . '-' . Str::slug($announcement->title);
        });
    }

    // belongsTo Major
    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}
