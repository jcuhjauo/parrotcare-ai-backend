<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'parrot_id',
        'visit_date',
        'weight_grams',
        'medications',
        'next_visit_date',
        'notes',
        'ai_confidence',
        'image_path',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'next_visit_date' => 'date',
        'medications' => 'array',
    ];

    public function parrot(): BelongsTo
    {
        return $this->belongsTo(Parrot::class);
    }
}