<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScanHealthRecordRequest;
use App\Http\Requests\StoreHealthRecordRequest;
use App\Models\HealthRecord;
use App\Services\HealthRecordAiScanner;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class HealthRecordScanController extends Controller
{
    public function __construct(
        private readonly HealthRecordAiScanner $scanner,
    ) {}

    public function scan(ScanHealthRecordRequest $request): JsonResponse
    {
        try {
            $result = $this->scanner->scan($request->file('image'));
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $path = $request->file('image')->store('health-records/pending', 'public');

        return response()->json([
            'draft' => $result,
            'image_path' => $path,
        ]);
    }

    public function store(StoreHealthRecordRequest $request): JsonResponse
    {
        $record = HealthRecord::create($request->validated());

        return response()->json(['record' => $record], 201);
    }
}