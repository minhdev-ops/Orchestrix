<?php

namespace App\Modules\AgriVerse\Services;

use App\Modules\AgriVerse\Models\AnalyticsEvent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AnalyticsService
{
    /**
     * Track an event
     */
    public function track(string $eventType, ?string $eventName = null, array $properties = []): ?AnalyticsEvent
    {
        if (! app()->runningInConsole() && ! Request::hasHeader('User-Agent')) {
            return null;
        }

        $data = [
            'user_id' => Auth::id(),
            'session_id' => session()->getId(),
            'event_type' => $eventType,
            'event_name' => $eventName,
            'properties' => $properties,
            'page_url' => Request::url(),
            'referrer_url' => Request::header('referer'),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'device_type' => $this->detectDeviceType(),
            'browser' => $this->detectBrowser(),
        ];

        return AnalyticsEvent::create($data);
    }

    /**
     * Track page view
     */
    public function trackPageView(string $page, array $meta = []): AnalyticsEvent
    {
        return $this->track('page_view', $page, $meta);
    }

    /**
     * Track product view
     */
    public function trackProductView(int $productId, array $meta = []): AnalyticsEvent
    {
        $event = $this->track('product_view', "product_{$productId}", array_merge($meta, [
            'product_id' => $productId,
        ]));

        // Update recently viewed
        $this->trackRecentlyViewed($productId);

        return $event;
    }

    /**
     * Track add to cart
     */
    public function trackAddToCart(int $productId, int $quantity, float $price): AnalyticsEvent
    {
        return $this->track('add_to_cart', 'add_to_cart', [
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price,
            'value' => $quantity * $price,
        ]);
    }

    /**
     * Track purchase
     */
    public function trackPurchase(int $orderId, float $total, array $items = []): AnalyticsEvent
    {
        return $this->track('purchase', 'purchase', [
            'order_id' => $orderId,
            'total' => $total,
            'items' => $items,
            'item_count' => count($items),
        ]);
    }

    /**
     * Track search
     */
    public function trackSearch(string $query, int $resultsCount = 0): AnalyticsEvent
    {
        return $this->track('search', 'search', [
            'query' => $query,
            'results_count' => $resultsCount,
        ]);
    }

    /**
     * Track recently viewed product
     */
    public function trackRecentlyViewed(int $productId): void
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        $conditions = ['product_id' => $productId];
        if ($userId) {
            $conditions['user_id'] = $userId;
        } else {
            $conditions['session_id'] = $sessionId;
        }

        DB::table('recently_viewed')->updateOrInsert(
            $conditions,
            [
                'view_count' => DB::raw('view_count + 1'),
                'viewed_at' => now(),
                'created_at' => now(),
            ]
        );

        // Cleanup: keep only last 50 items per user
        $this->cleanupRecentlyViewed($userId, $sessionId);
    }

    /**
     * Get recently viewed products
     */
    public function getRecentlyViewed(int $limit = 20): Collection
    {
        $userId = auth()->id();
        $sessionId = session()->getId();

        return DB::table('recently_viewed')
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->join('products', 'recently_viewed.product_id', '=', 'products.id')
            ->where('products.status', 'published')
            ->select('recently_viewed.*', 'products.name', 'products.price', 'products.image')
            ->orderByDesc('recently_viewed.viewed_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Cleanup recently viewed (keep only 50)
     */
    protected function cleanupRecentlyViewed(?int $userId, string $sessionId): void
    {
        $query = DB::table('recently_viewed');

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $ids = $query->orderByDesc('viewed_at')
            ->limit(50)
            ->pluck('id');

        $query->whereNotIn('id', $ids)->delete();
    }

    /**
     * Get dashboard stats
     */
    public function getDashboardStats(string $startDate, string $endDate): array
    {
        $events = AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_events' => (clone $events)->count(),
            'unique_users' => (clone $events)->distinct('user_id')->count('user_id'),
            'page_views' => (clone $events)->pageView()->count(),
            'product_views' => (clone $events)->productView()->count(),
            'add_to_carts' => (clone $events)->cart()->count(),
            'purchases' => (clone $events)->purchase()->count(),
            'searches' => (clone $events)->search()->count(),
            'conversion_rate' => $this->calculateConversionRate($startDate, $endDate),
            'top_pages' => $this->getTopPages($startDate, $endDate),
            'top_products' => $this->getTopProducts($startDate, $endDate),
            'top_searches' => $this->getTopSearches($startDate, $endDate),
            'device_breakdown' => $this->getDeviceBreakdown($startDate, $endDate),
            'hourly_traffic' => $this->getHourlyTraffic($startDate, $endDate),
        ];
    }

    /**
     * Calculate conversion rate
     */
    protected function calculateConversionRate(string $startDate, string $endDate): float
    {
        $visits = AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate])
            ->pageView()
            ->distinct('session_id')
            ->count('session_id');

        $purchases = AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate])
            ->purchase()
            ->distinct('session_id')
            ->count('session_id');

        if ($visits === 0) {
            return 0;
        }

        return round(($purchases / $visits) * 100, 2);
    }

    /**
     * Get top pages
     */
    protected function getTopPages(string $startDate, string $endDate, int $limit = 10): array
    {
        return AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate])
            ->pageView()
            ->select('event_name', DB::raw('COUNT(*) as views'))
            ->groupBy('event_name')
            ->orderByDesc('views')
            ->limit($limit)
            ->pluck('views', 'event_name')
            ->toArray();
    }

    /**
     * Get top products
     */
    protected function getTopProducts(string $startDate, string $endDate, int $limit = 10): array
    {
        return AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate])
            ->productView()
            ->select('properties->product_id as product_id', DB::raw('COUNT(*) as views'))
            ->groupBy('properties->product_id')
            ->orderByDesc('views')
            ->limit($limit)
            ->pluck('views', 'product_id')
            ->toArray();
    }

    /**
     * Get top searches
     */
    protected function getTopSearches(string $startDate, string $endDate, int $limit = 10): array
    {
        return AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate])
            ->search()
            ->select('properties->query as query', DB::raw('COUNT(*) as count'))
            ->groupBy('properties->query')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('count', 'query')
            ->toArray();
    }

    /**
     * Get device breakdown
     */
    protected function getDeviceBreakdown(string $startDate, string $endDate): array
    {
        return AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate])
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();
    }

    /**
     * Get hourly traffic
     */
    protected function getHourlyTraffic(string $startDate, string $endDate): array
    {
        return AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate])
            ->pageView()
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();
    }

    /**
     * Detect device type
     */
    protected function detectDeviceType(): string
    {
        $ua = strtolower(request()->userAgent());
        if (strpos($ua, 'mobile') !== false || strpos($ua, 'android') !== false) {
            return 'mobile';
        }
        if (strpos($ua, 'tablet') !== false || strpos($ua, 'ipad') !== false) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Detect browser
     */
    protected function detectBrowser(): string
    {
        $ua = request()->userAgent();
        if (strpos($ua, 'Firefox') !== false) {
            return 'Firefox';
        }
        if (strpos($ua, 'Edg') !== false) {
            return 'Edge';
        }
        if (strpos($ua, 'Chrome') !== false) {
            return 'Chrome';
        }
        if (strpos($ua, 'Safari') !== false) {
            return 'Safari';
        }

        return 'Other';
    }
}
