<?php

use App\Http\Controllers\HealthRecordScanController;
use Illuminate\Support\Facades\Route;

Route::get('/records', [HealthRecordScanController::class, 'index']);
Route::post('/records/scan', [HealthRecordScanController::class, 'scan']);
Route::post('/records', [HealthRecordScanController::class, 'store']);
Route::put('/records/{healthRecord}', [HealthRecordScanController::class, 'update']);
Route::delete('/records/{healthRecord}', [HealthRecordScanController::class, 'destroy']);