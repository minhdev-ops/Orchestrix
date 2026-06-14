<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Inertia\Inertia;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Specimen;
use App\Modules\AgriVerse\Models\SupportFaq;
use App\Modules\AgriVerse\Models\QuizQuestion;
use App\Modules\AgriVerse\Models\DiagnosticSymptom;
use App\Modules\AgriVerse\Models\SustainabilityReport;
use App\Modules\AgriVerse\Models\JournalArticle;

class PageController
{
    public function accountSettings()
    {
        $user = auth()->user();
        $notificationPrefs = $user?->notification_preferences ?? [
            'push' => true,
            'email' => false,
            'order_updates' => true,
            'promotions' => false,
        ];

        return Inertia::render('Marketplace/Account/Settings', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'gender' => $user->gender,
                'bio' => $user->bio,
            ],
            'notificationPreferences' => $notificationPrefs,
        ]);
    }

    public function saveNotificationPreferences(Request $request)
    {
        $data = $request->validate([
            'push' => 'boolean',
            'email' => 'boolean',
            'order_updates' => 'boolean',
            'promotions' => 'boolean',
        ]);

        $user = auth()->user();
        if ($user) {
            $user->update(['notification_preferences' => $data]);
        }

        return response()->json(['ok' => true]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user->update($data);

        return response()->json(['ok' => true, 'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'bio' => $user->bio,
        ]]);
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'oldpass' => 'required',
            'newpass' => 'required|min:8',
            'repass' => 'required|same:newpass',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($data['oldpass'], $user->password)) {
            return response()->json(['mes' => ['oldpass' => ['Mật khẩu hiện tại không đúng']]], 422);
        }

        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($data['newpass'])]);

        return response()->json(['ok' => true]);
    }

    public function forum()
    {
        return Inertia::render('Marketplace/Forum/Index');
    }

    public function profile()
    {
        $user = auth()->user();

        $sellerStore = null;
        $totalOrdersReceived = 0;

        if ($user && $user->isSeller()) {
            $sellerStore = Store::where('owner_id', $user->id)
                ->withCount('products')
                ->first();

            if ($sellerStore) {
                $totalOrdersReceived = Order::where('seller_id', $user->id)->count();
            }
        }

        $recentOrders = Order::where('buyer_id', $user->id)
            ->with('product:id,name')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($o) => [
                'id' => $o->id,
                'total_amount' => $o->total_amount,
                'status' => $o->status,
                'created_at' => $o->created_at,
                'product' => $o->product ? ['id' => $o->product->id, 'name' => $o->product->name] : null,
            ]);

        return Inertia::render('Marketplace/Profile/Index', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'bio' => $user->bio,
                'role' => $user->role,
                'seller_verified_at' => $user->seller_verified_at,
                'created_at' => $user->created_at,
            ],
            'sellerStore' => $sellerStore,
            'totalOrdersReceived' => $totalOrdersReceived,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function garden()
    {
        $user = auth()->user();
        $specimens = $user
            ? Specimen::where('user_id', $user->id)->get()
            : collect([]);

        $wishlist = $user
            ? \App\Modules\AgriVerse\Models\Wishlist::where('user_id', $user->id)
                ->with('product:id,name,price,image')
                ->get()
                ->map(fn($w) => [
                    'id' => $w->id,
                    'product_id' => $w->product_id,
                    'image' => $w->product?->image,
                    'name' => $w->product?->name,
                ])
            : collect([]);

        $totalSpecimens = $specimens->count();
        $hydratedCount = $specimens->where('hydration_error', false)->count();
        $greenFingers = $totalSpecimens > 0
            ? round(($hydratedCount / $totalSpecimens) * 100)
            : 0;

        $grade = $greenFingers >= 80 ? 'A+' : ($greenFingers >= 60 ? 'A' : ($greenFingers >= 40 ? 'B' : 'C'));
        $tier = match ($grade) {
            'A+' => 'Cấp Cao cấp',
            'A' => 'Cấp Khá',
            'B' => 'Cấp Trung bình',
            default => 'Cấp Mới',
        };

        return Inertia::render('Marketplace/Garden/Index', [
            'specimens' => $specimens,
            'wishlist' => $wishlist,
            'heroStats' => [
                'greenFingers' => $greenFingers,
                'activeSpecimens' => $totalSpecimens,
                'grade' => $grade,
                'tier' => $tier,
                'userName' => $user?->name ?? 'Bạn',
            ],
        ]);
    }

    public function notifications()
    {
        $user = auth()->user();
        $notifications = $user
            ? $user->notifications()->latest()->paginate(20)
            : new LengthAwarePaginator([], 0, 20);

        return Inertia::render('Marketplace/Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }

    public function quiz()
    {
        $questions = QuizQuestion::orderBy('sort_order')->get();

        return Inertia::render('Marketplace/Quiz/Index', [
            'questions' => $questions,
        ]);
    }

    public function diagnostic()
    {
        $symptoms = DiagnosticSymptom::orderBy('sort_order')->get()->pluck('name');

        return Inertia::render('Marketplace/Diagnostic/Index', [
            'symptoms' => $symptoms,
        ]);
    }

    public function tracking()
    {
        return Inertia::render('Marketplace/Tracking/Index');
    }

    public function support()
    {
        $faqs = SupportFaq::where('is_published', true)->orderBy('sort_order')->get()->groupBy('category');

        return Inertia::render('Marketplace/Support/Index', [
            'faqs' => $faqs,
            'stats' => [
                'successRate' => 98.4,
                'activeTickets' => 12,
                'repositoryArticles' => 4200,
            ],
        ]);
    }

    public function sustainability()
    {
        $report = SustainabilityReport::where('year', now()->year)->first()
            ?? SustainabilityReport::latest()->first();

        return Inertia::render('Marketplace/Sustainability/Index', [
            'report' => $report,
        ]);
    }

    public function journal(Request $request)
    {
        $article = null;
        if ($request->route('article')) {
            $article = JournalArticle::where('slug', $request->route('article'))
                ->orWhere('id', $request->route('article'))
                ->first();
        }
        if (!$article) {
            $article = JournalArticle::latest('published_at')->first();
        }

        return Inertia::render('Marketplace/Journal/Show', [
            'article' => $article,
        ]);
    }

    public function notFound()
    {
        return Inertia::render('Marketplace/Errors/NotFound');
    }

    public function arViewer($productId)
    {
        $product = \App\Modules\AgriVerse\Models\Product::with(['categories', 'store', 'reviews.user:id,name'])
            ->findOrFail($productId);

        return Inertia::render('Marketplace/AR/Index', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'compare_price' => $product->compare_price,
                'description' => $product->description,
                'stock' => $product->stock,
                'height' => $product->height,
                'model_3d_url' => $product->model_3d_url,
                'categories' => $product->categories->map(fn($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                ]),
                'store' => $product->store ? [
                    'id' => $product->store->id,
                    'name' => $product->store->name,
                ] : null,
            ],
            'modelUrl' => $product->model_3d_url ?: '/models/bonsai.glb',
        ]);
    }
}
