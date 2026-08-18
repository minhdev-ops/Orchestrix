<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Services\PlantDiagnosisService;
use Illuminate\Http\Request;

class DiagnosticController
{
    public function analyze(Request $request, PlantDiagnosisService $service)
    {
        $data = $request->validate([
            'symptoms' => 'required|array|min:1',
            'symptoms.*' => 'string|max:100',
            'plant_name' => 'nullable|string|max:255',
        ]);

        return response()->json($service->analyze($data['symptoms'], $data['plant_name'] ?? null));
    }
}