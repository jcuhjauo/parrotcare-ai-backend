<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHealthRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clinic_name' => ['nullable', 'string'],
            'clinic_phone' => ['nullable', 'string'],
            'clinic_address' => ['nullable', 'string'],
            'owner_name' => ['nullable', 'string'],
            'owner_phone' => ['nullable', 'string'],
            'pet_name' => ['nullable', 'string'],
            'species' => ['nullable', 'string'],
            'visit_date' => ['nullable', 'date'],
            'weight_grams' => ['nullable', 'numeric'],
            'medications' => ['nullable', 'array'],
            'medications.*.name' => ['required_with:medications', 'string'],
            'medications.*.frequency' => ['nullable', 'string'],
            'medications.*.duration_days' => ['nullable', 'integer'],
            'line_items' => ['nullable', 'array'],
            'line_items.*.item' => ['required_with:line_items', 'string'],
            'line_items.*.amount' => ['nullable', 'numeric'],
            'total_amount' => ['nullable', 'numeric'],
            'next_visit_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}