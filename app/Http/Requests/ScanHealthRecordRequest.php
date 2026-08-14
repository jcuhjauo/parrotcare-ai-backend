<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanHealthRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'parrot_id' => ['required', 'integer', 'exists:parrots,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => '請上傳一張醫療文件照片',
            'image.max' => '圖片檔案不能超過 5MB，請重新拍攝或壓縮後再上傳',
        ];
    }
}