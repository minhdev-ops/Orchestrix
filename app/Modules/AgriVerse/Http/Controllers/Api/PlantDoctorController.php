<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\PlantDiagnosis;
use App\Modules\AgriVerse\Services\AIPlantDoctorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlantDoctorController
{
    public function __construct(
        protected AIPlantDoctorService $aiService,
    ) {}

    public function diagnose(Request $request): JsonResponse
    {
        $data = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'symptoms' => 'nullable|string|max:1000',
        ]);

        try {
            $result = $this->aiService->diagnose($data['image'], $data['symptoms'] ?? null);

            $path = $data['image']->store('plant-diagnoses', 'public');

            $diagnosis = PlantDiagnosis::create([
                'user_id' => $request->user()->id,
                'image_path' => $path,
                'plant_name' => $result['plant_name'],
                'disease_name' => $result['disease_name'],
                'confidence' => $result['confidence'],
                'severity' => $result['severity'],
                'description' => $result['description'],
                'treatments' => $result['treatments'],
                'prevention' => $result['prevention'],
                'raw_response' => $result['raw_response'],
                'provider' => $result['provider'],
            ]);

            return response()->json([
                'diagnosis' => [
                    'id' => $diagnosis->id,
                    'uuid' => $diagnosis->uuid,
                    'image_url' => asset('storage/' . $path),
                    'plant_name' => $diagnosis->plant_name,
                    'disease_name' => $diagnosis->disease_name,
                    'confidence' => $diagnosis->confidence,
                    'severity' => $diagnosis->severity,
                    'description' => $diagnosis->description,
                    'treatments' => $diagnosis->treatments,
                    'prevention' => $diagnosis->prevention,
                    'provider' => $diagnosis->provider,
                    'created_at' => $diagnosis->created_at->toIso8601String(),
                ],
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Chẩn đoán thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function history(Request $request): AnonymousResourceCollection
    {
        $diagnoses = PlantDiagnosis::where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->per_page ?? 15);

        return JsonResource::collection($diagnoses);
    }

    public function show(PlantDiagnosis $diagnosis): JsonResource
    {
        if ($diagnosis->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return JsonResource::make($diagnosis);
    }

    public function destroy(PlantDiagnosis $diagnosis): JsonResponse
    {
        if ($diagnosis->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $diagnosis->delete();

        return response()->json(['message' => 'Đã xóa kết quả chẩn đoán']);
    }
}
