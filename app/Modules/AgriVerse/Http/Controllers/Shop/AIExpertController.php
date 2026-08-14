<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Services\AIExpertService;
use Illuminate\Http\Request;

class AIExpertController
{
    public function chat(Request $request, AIExpertService $service)
    {
        $data = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $result = $service->chat($data['message']);

        return response()->json($result);
    }
}
