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
        'clinic_name',
        'clinic_phone',
        'clinic_address',
        'owner_name',
        'owner_phone',
        'pet_name',
        'species',
        'visit_date',
        'weight_grams',
        'medications',
        'line_items',
        'total_amount',
        'next_visit_date',
        'notes',
        'ai_confidence',
        'image_path',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'next_visit_date' => 'date',
        'medications' => 'array',
        'line_items' => 'array',
    ];

    public function parrot(): BelongsTo
    {
        return $this->belongsTo(Parrot::class);
    }
}