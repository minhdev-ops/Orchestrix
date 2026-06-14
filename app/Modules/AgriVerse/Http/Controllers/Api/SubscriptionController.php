<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\Subscription;
use App\Modules\AgriVerse\Http\Resources\SubscriptionResource;
use App\Modules\AgriVerse\Http\Requests\StoreSubscriptionRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;

class SubscriptionController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Subscription::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->latest()->paginate($request->per_page ?? 15);

        return SubscriptionResource::collection($subscriptions);
    }

    public function show(Subscription $subscription): SubscriptionResource
    {
        return SubscriptionResource::make($subscription);
    }

    public function store(StoreSubscriptionRequest $request): SubscriptionResource
    {
        $subscription = Subscription::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'features' => $request->features,
            'status' => $request->status ?? 'active',
        ]);

        return SubscriptionResource::make($subscription);
    }

    public function cancel(Request $request, Subscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Subscription cancelled.']);
    }
}
