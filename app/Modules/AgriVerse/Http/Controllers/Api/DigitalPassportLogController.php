<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Http\Requests\StoreDigitalPassportLogRequest;
use App\Modules\AgriVerse\Http\Resources\DigitalPassportLogResource;
use App\Modules\AgriVerse\Models\DigitalPassportLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DigitalPassportLogController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = DigitalPassportLog::query()->with('performer');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        return DigitalPassportLogResource::collection($query->latest()->paginate($request->per_page ?? 15));
    }

    public function show(DigitalPassportLog $log): DigitalPassportLogResource
    {
        return DigitalPassportLogResource::make($log->load('performer'));
    }

    public function store(StoreDigitalPassportLogRequest $request): DigitalPassportLogResource
    {
        $log = DigitalPassportLog::create([
            'product_id' => $request->product_id,
            'action' => $request->action,
            'data' => $request->data,
            'performed_by' => $request->user()->id,
        ]);

        return DigitalPassportLogResource::make($log->load('performer'));
    }
}
