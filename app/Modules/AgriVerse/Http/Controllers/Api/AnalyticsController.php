<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\AgriVerse\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Track an event
     */
    public function track(Request $request)
    {
        $request->validate([
            'event_type' => 'required|string|in:page_view,product_view,add_to_cart,purchase,search,custom',
            'event_name' => 'nullable|string',
            'properties' => 'nullable|array',
        ]);

        $event = $this->analyticsService->track(
            $request->event_type,
            $request->event_name,
            $request->properties ?? []
        );

        return response()->json(['success' => true, 'event_id' => $event->id]);
    }

    /**
     * Track page view
     */
    public function pageView(Request $request)
    {
        $request->validate([
            'page' => 'required|string',
        ]);

        $this->analyticsService->trackPageView($request->page, $request->meta ?? []);

        return response()->json(['success' => true]);
    }

    /**
     * Track product view
     */
    public function productView(Request $request, int $productId)
    {
        $this->analyticsService->trackProductView($productId, $request->meta ?? []);

        return response()->json(['success' => true]);
    }

    /**
     * Track add to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $this->analyticsService->trackAddToCart(
            $request->product_id,
            $request->quantity,
            $request->price
        );

        return response()->json(['success' => true]);
    }

    /**
     * Get recently viewed products
     */
    public function recentlyViewed(Request $request)
    {
        $recentlyViewed = $this->analyticsService->getRecentlyViewed($request->limit ?? 20);

        return response()->json(['data' => $recentlyViewed]);
    }
}
