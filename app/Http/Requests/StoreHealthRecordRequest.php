<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHealthRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parrot_id' => ['required', 'integer', 'exists:parrots,id'],
            'visit_date' => ['nullable', 'date'],
            'weight_grams' => ['nullable', 'numeric'],
            'medications' => ['nullable', 'array'],
            'medications.*.name' => ['required_with:medications', 'string'],
            'medications.*.frequency' => ['nullable', 'string'],
            'medications.*.duration_days' => ['nullable', 'integer'],
            'next_visit_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'image_path' => ['required', 'string'],
        ];
    }
}