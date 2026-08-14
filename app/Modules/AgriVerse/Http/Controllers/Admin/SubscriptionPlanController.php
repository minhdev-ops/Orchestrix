<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Modules\AgriVerse\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionPlanController
{
    public function index()
    {
        $plans = SubscriptionPlan::latest()->paginate(15);

        return Inertia::render('Admin/SubscriptionPlans/Index', [
            'plans' => $plans,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/SubscriptionPlans/Form', [
            'plan' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'limit_3d_models' => 'required|integer|min:0',
            'price_per_month' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.agriverse.plans.index')
            ->with('success', 'Gói đăng ký đã được tạo.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return Inertia::render('Admin/SubscriptionPlans/Form', [
            'plan' => $plan,
        ]);
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'limit_3d_models' => 'required|integer|min:0',
            'price_per_month' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'status' => 'required|in:active,inactive',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.agriverse.plans.index')
            ->with('success', 'Gói đăng ký đã được cập nhật.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.agriverse.plans.index')
            ->with('success', 'Gói đăng ký đã được xóa.');
    }
}
