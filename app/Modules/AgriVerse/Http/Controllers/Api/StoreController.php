<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Http\Resources\StoreResource;
use App\Modules\AgriVerse\Models\Store;
use Illuminate\Http\Request;

class StoreController
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['data' => []]);
        }

        $query = Store::query()->with('owner')->withCount('products');

        if ($user->hasRole('seller')) {
            $query->where('owner_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stores = $query->latest()->paginate($request->per_page ?? 15);

        return StoreResource::collection($stores);
    }

    public function show(Store $store): StoreResource
    {
        $store->load('owner')->loadCount('products');

        return StoreResource::make($store);
    }
}
