<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Services\WAQIService;
use Illuminate\Http\Request;

class AirQualityController
{
    public function index(Request $request, WAQIService $waqi)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $data = $waqi->getByCoordinates(
            (float) $request->lat,
            (float) $request->lng
        );

        return response()->json($data);
    }
}
