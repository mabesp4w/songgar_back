<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, HasUuids;

    // hasOne user
    function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    // belongsTo Major
    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    // hasMany Structural
    public function structural()
    {
        return $this->hasMany(Structural::class);
    }
}
