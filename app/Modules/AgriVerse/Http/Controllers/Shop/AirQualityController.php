<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use App\Modules\AgriVerse\Services\WAQIService;

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
