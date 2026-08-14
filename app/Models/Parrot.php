<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parrot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'species',
        'birth_date',
    ];

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }
}