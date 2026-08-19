<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class HealthRecordAiScanner
{
    private const SYSTEM_PROMPT = <<<'PROMPT'
你是一個專門辨識寵物醫療文件（收據、健檢報告、藥單）的助手。使用者會上傳一張照片。
請只回傳一個 JSON 物件，不要有任何其他文字、不要用 markdown 包裹、不要加註解。
若某欄位無法從圖片中判讀，該欄位請填 null，不要猜測或編造內容。

JSON 格式如下：
{
  "clinic_name": "院所名稱或 null",
  "clinic_phone": "院所電話或 null",
  "clinic_address": "院所地址或 null",
  "owner_name": "飼主姓名或 null",
  "owner_phone": "飼主電話或 null",
  "pet_name": "寵物名字或 null",
  "species": "物種/品種或 null",
  "visit_date": "YYYY-MM-DD 或 null",
  "weight_grams": "數字或 null",
  "medications": [
    { "name": "藥名", "frequency": "服用頻率", "duration_days": "數字或 null" }
  ],
  "line_items": [
    { "item": "項目名稱", "amount": "數字" }
  ],
  "total_amount": "總計金額數字或 null",
  "next_visit_date": "YYYY-MM-DD 或 null",
  "notes": "其他備註原文，字串或 null",
  "confidence": "high 或 medium 或 low"
}
PROMPT;

    private string $apiKey;

    public function __construct(
        string $apiKey = '',
        private readonly string $model = 'gemini-2.5-flash',
    ) {
        $this->apiKey = $apiKey ?: config('services.gemini.key');
    }

    public function scan(UploadedFile $image): array
    {
        $base64 = base64_encode(file_get_contents($image->getRealPath()));
        $mediaType = $image->getMimeType();

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        $response = Http::withHeaders([
            'x-goog-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        [
                            'inline_data' => [
                                'mime_type' => $mediaType,
                                'data' => $base64,
                            ],
                        ],
                        [
                            'text' => self::SYSTEM_PROMPT . "\n\n請辨識這張醫療文件並回傳 JSON。",
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
            ],
        ]);

        if ($response->failed()) {
            Log::error('Gemini scan API failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new RuntimeException('AI 辨識服務暫時無法使用，請稍後再試');
        }

        $rawText = data_get($response->json(), 'candidates.0.content.parts.0.text', '');
        $rawText = trim(preg_replace('/^```json|```$/m', '', $rawText));

        $decoded = json_decode($rawText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Gemini returned invalid JSON', ['raw' => $rawText]);
            throw new RuntimeException('AI 回傳格式異常，請重新拍攝更清晰的照片後再試');
        }

        return $this->validateSchema($decoded);
    }

    private function validateSchema(array $data): array
    {
        $isValidDate = fn ($v) => $v === null || preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $v);
        $toStringOrNull = fn ($v) => is_string($v) && $v !== '' ? $v : null;

        return [
            'clinic_name' => $toStringOrNull($data['clinic_name'] ?? null),
            'clinic_phone' => $toStringOrNull($data['clinic_phone'] ?? null),
            'clinic_address' => $toStringOrNull($data['clinic_address'] ?? null),
            'owner_name' => $toStringOrNull($data['owner_name'] ?? null),
            'owner_phone' => $toStringOrNull($data['owner_phone'] ?? null),
            'pet_name' => $toStringOrNull($data['pet_name'] ?? null),
            'species' => $toStringOrNull($data['species'] ?? null),
            'visit_date' => $isValidDate($data['visit_date'] ?? null) ? $data['visit_date'] : null,
            'weight_grams' => is_numeric($data['weight_grams'] ?? null) ? (float) $data['weight_grams'] : null,
            'medications' => $this->validateMedications($data['medications'] ?? []),
            'line_items' => $this->validateLineItems($data['line_items'] ?? []),
            'total_amount' => is_numeric($data['total_amount'] ?? null) ? (float) $data['total_amount'] : null,
            'next_visit_date' => $isValidDate($data['next_visit_date'] ?? null) ? $data['next_visit_date'] : null,
            'notes' => $toStringOrNull($data['notes'] ?? null),
            'confidence' => in_array($data['confidence'] ?? null, ['high', 'medium', 'low'], true)
                ? $data['confidence']
                : 'low',
        ];
    }

    private function validateMedications(mixed $medications): array
    {
        if (!is_array($medications)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($med) {
            if (!is_array($med) || empty($med['name'])) {
                return null;
            }

            return [
                'name' => (string) $med['name'],
                'frequency' => isset($med['frequency']) ? (string) $med['frequency'] : null,
                'duration_days' => is_numeric($med['duration_days'] ?? null) ? (int) $med['duration_days'] : null,
            ];
        }, $medications)));
    }

    private function validateLineItems(mixed $items): array
    {
        if (!is_array($items)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($item) {
            if (!is_array($item) || empty($item['item'])) {
                return null;
            }

            return [
                'item' => (string) $item['item'],
                'amount' => is_numeric($item['amount'] ?? null) ? (float) $item['amount'] : null,
            ];
        }, $items)));
    }
}