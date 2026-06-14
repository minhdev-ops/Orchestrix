<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\DigitalPassportLog;
use App\Modules\AgriVerse\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DigitalPassportController
{
    public function index(Product $product): AnonymousResourceCollection
    {
        $logs = DigitalPassportLog::with('performer')
            ->where('product_id', $product->id)
            ->latest()
            ->get();

        return JsonResource::collection($logs);
    }

    public function store(Request $request, Product $product): JsonResource
    {
        $data = $request->validate([
            'action' => 'required|string|max:255',
            'data' => 'nullable|array',
        ]);

        $log = DigitalPassportLog::create([
            'product_id' => $product->id,
            'action' => $data['action'],
            'data' => $data['data'] ?? [],
            'performed_by' => $request->user()->id,
        ]);

        $log->load('performer');

        return JsonResource::make($log);
    }

    public function update(Request $request, DigitalPassportLog $passport): JsonResource
    {
        $data = $request->validate([
            'action' => 'nullable|string|max:255',
            'data' => 'nullable|array',
        ]);

        $passport->update(array_filter($data, fn($v) => !is_null($v)));
        $passport->load('performer');

        return JsonResource::make($passport);
    }

    public function destroy(DigitalPassportLog $passport)
    {
        $passport->delete();
        return response()->json(['message' => 'Passport log deleted.']);
    }

    public function summary(Product $product): JsonResource
    {
        $logs = DigitalPassportLog::where('product_id', $product->id)->get();

        return JsonResource::make([
            'total_events' => $logs->count(),
            'recent_events' => $logs->sortByDesc('created_at')->take(5)->values(),
            'event_types' => $logs->groupBy('action')->map->count(),
            'product' => $product->only(['id', 'name', 'technical_specs']),
        ]);
    }
}
