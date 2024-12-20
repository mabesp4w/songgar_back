<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Structural extends Model
{
    use HasFactory, HasUuids;

    // belongsTo employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
