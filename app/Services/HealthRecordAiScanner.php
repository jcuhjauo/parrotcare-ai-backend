<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class HealthRecordAiScanner
{
    /**
     * 這是「假資料版本」，先不呼叫真的 AI API，方便你在不花錢的情況下
     * 把上傳、確認畫面、儲存這整條流程跑通。
     * 之後想接真的 AI，把這個 method 內容換成呼叫 API 的邏輯即可，
     * scan() 的輸入輸出格式完全不變，其他檔案都不用動。
     */
    public function scan(UploadedFile $image): array
    {
        // 模擬 AI 處理需要一點時間，讓前端的 loading 動畫有機會顯示出來
        usleep(800000); // 0.8 秒

        return [
            'visit_date' => now()->subDays(2)->format('Y-m-d'),
            'weight_grams' => 45.5,
            'medications' => [
                [
                    'name' => '維他命補充劑',
                    'frequency' => '每日一次',
                    'duration_days' => 7,
                ],
            ],
            'next_visit_date' => now()->addDays(14)->format('Y-m-d'),
            'notes' => '這是測試用的假資料，尚未接上真正的 AI 辨識服務。',
            'confidence' => 'medium',
        ];
    }
}