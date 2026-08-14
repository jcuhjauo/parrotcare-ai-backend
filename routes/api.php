<?php

use App\Http\Controllers\HealthRecordScanController;
use Illuminate\Support\Facades\Route;

Route::post('/records/scan', [HealthRecordScanController::class, 'scan']);
Route::post('/records', [HealthRecordScanController::class, 'store']);