# ParrotCare AI — 醫療報告智能掃描

一個結合 AI 視覺辨識的寵物照護後端 API，讓使用者拍照上傳鸚鵡的醫療收據/健檢報告，AI 自動擷取關鍵資訊（就診日期、體重、用藥、複診日期）並轉換成結構化資料，省去手動輸入的麻煩。

這是我在準備 PHP 後端工程師職缺面試時，為了展示「AI 應用串接能力」而做的個人練習專案，結合了我養牡丹鸚鵡的真實需求。

## 這個專案展示了什麼

- **後端 API 設計**：Laravel 的 Controller / FormRequest / Service 分層架構
- **第三方 AI 服務整合**：呼叫支援視覺輸入的 LLM API，並要求模型回傳結構化 JSON
- **不信任外部輸入的工程紀律**：AI 回傳的 JSON 不會直接寫入資料庫，後端會逐欄位做型別驗證（`HealthRecordAiScanner::validateSchema`），格式不符的欄位一律歸零而非讓髒資料進系統
- **前後端協作流程**：AI 結果先當作「草稿」回傳給前端，使用者確認/修正後才正式儲存——因為醫療相關資訊出錯的代價較高，不能讓 AI 的輸出直接當作最終答案

## 技術棧

**後端**
- Laravel 13 / PHP 8.4
- SQLite（本地開發）
- Laravel Sanctum（API 認證，規劃中）

**前端**
- Next.js（App Router）/ TypeScript
- Tailwind CSS
- Client-side 圖片壓縮（上傳前先在瀏覽器端用 Canvas 壓縮，減少 API 成本與等待時間）

## 系統流程

```
使用者拍照/選圖
   → 前端壓縮圖片
   → 上傳至 POST /api/records/scan
   → 後端呼叫 AI 視覺模型，取得結構化 JSON 草稿
   → 後端驗證 JSON schema（型別檢查，不信任 AI 輸出）
   → 回傳草稿給前端
   → 使用者於確認畫面檢視/修正欄位
   → 送出 POST /api/records
   → 正式寫入資料庫
```

## 資料表結構

- `parrots`：鸚鵡基本資料（名字、品種、出生日期）
- `health_records`：健康紀錄，包含就診日期、體重、用藥、複診日期、AI 辨識信心分級，並關聯到 `parrots`

## 本地開發

```bash
# 後端
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # Windows 用 New-Item database/database.sqlite -ItemType File
php artisan migrate
php artisan serve

# 前端（另開終端機）
cd ../parrotcare-frontend
npm install
npm run dev
```

需要在 `.env` 設定 `ANTHROPIC_API_KEY` 才能使用真正的 AI 辨識功能；未設定時 `HealthRecordAiScanner` 可切換為回傳假資料的版本，方便在不產生 API 費用的情況下開發與測試前後端流程。

## 已知待辦

- [ ] API 尚未加上 `auth:sanctum` 認證中介層
- [ ] `ai_confidence` 欄位目前未被 `StoreHealthRecordRequest` 的驗證規則收錄，儲存時會遺失
- [ ] 尚未串接複診日期的自動提醒通知
- [ ] 上傳圖片的儲存路徑（`storage/app/public`）尚未設定 symbolic link

## 專案動機

我養了一隻牡丹鸚鵡，每次帶去看特寵獸醫，藥單、健檢報告都是紙本，回家常常忘記餵藥時間或複診日期。這個功能就是為了解決這個真實的生活痛點而做的。