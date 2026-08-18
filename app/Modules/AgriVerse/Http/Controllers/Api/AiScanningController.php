<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Jobs\ProcessAiScanning;
use App\Modules\AgriVerse\Models\AiScanningJob;
use App\Modules\AgriVerse\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class AiScanningController
{
    public function requestScan(Request $request): JsonResource
    {
        $data = $request->validate([
            'store_id' => 'required|integer|exists:stores,id',
            'source_video_url' => 'required|url|max:2048',
            'metadata' => 'nullable|array',
        ]);

        // Verify ownership
        $store = Store::findOrFail($data['store_id']);
        if ($store->owner_id !== $request->user()->id && ! $request->user()->hasRole('admin')) {
            abort(403, 'You do not own this store.');
        }

        $job = AiScanningJob::create([
            'store_id' => $data['store_id'],
            'source_video_url' => $data['source_video_url'],
            'status' => 'pending',
            'metadata' => $data['metadata'] ?? [],
        ]);

        ProcessAiScanning::dispatch($job);

        return JsonResource::make($job);
    }

    public function scanStatus(Request $request, AiScanningJob $job): JsonResource
    {
        $user = $request->user();
        if (! $user || $job->store->owner_id !== $user->id) {
            abort(403);
        }

        return JsonResource::make($job);
    }

    public function myJobs(Request $request): AnonymousResourceCollection
    {
        $storeIds = Store::where('owner_id', $request->user()->id)->pluck('id');

        $jobs = AiScanningJob::whereIn('store_id', $storeIds)
            ->latest()
            ->paginate($request->per_page ?? 15);

        return JsonResource::collection($jobs);
    }
}
