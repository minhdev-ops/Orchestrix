<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Http\Resources\SubscriptionPlanResource;
use App\Modules\AgriVerse\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController
{
    public function index(Request $request)
    {
        $query = SubscriptionPlan::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $plans = $query->latest()->paginate($request->per_page ?? 15);

        return SubscriptionPlanResource::collection($plans);
    }

    public function show(SubscriptionPlan $plan): SubscriptionPlanResource
    {
        return SubscriptionPlanResource::make($plan);
    }
}
