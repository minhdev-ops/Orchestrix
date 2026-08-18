<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Models\User;
use App\Modules\AgriVerse\Models\DiagnosticSymptom;
use App\Modules\AgriVerse\Models\JournalArticle;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\QuizQuestion;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\SupportFaq;
use App\Modules\AgriVerse\Models\SustainabilityReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

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

        if (! Hash::check($data['oldpass'], $user->password)) {
            return response()->json(['mes' => ['oldpass' => ['Mật khẩu hiện tại không đúng']]], 422);
        }

        $user->update(['password' => Hash::make($data['newpass'])]);

        return response()->json(['ok' => true]);
    }

    public function forum()
    {
        return Inertia::render('Marketplace/Forum/Index');
    }

    public function profile(?User $user = null)
    {
        $user = $user ?? auth()->user();

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
            ->map(fn ($o) => [
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

    public function garden(): RedirectResponse
    {
        return Redirect::route('agriverse.shop.garden.index');
    }

    // Legacy garden method replaced by GardenController; kept for reference.
    // Old body rendered Marketplace/Garden/Index with specimens + wishlist + heroStats.

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
        $symptoms = DiagnosticSymptom::orderBy('sort_order')
            ->get(['id', 'name', 'description', 'category'])
            ->map(fn ($s) => [
                'name' => $s->name,
                'description' => $s->description,
                'category' => $s->category,
            ]);

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

    public function journalIndex(Request $request)
    {
        $query = JournalArticle::query();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('abstract', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhere('tag', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 12);
        $paginator = $query->latest('published_at')->paginate($perPage);

        $articles = $paginator->map(fn ($a) => [
            'id' => $a->id,
            'title' => $a->title,
            'slug' => $a->slug,
            'tag' => $a->tag,
            'author_name' => $a->author_name,
            'abstract' => $a->abstract,
            'hero_image_url' => $a->hero_image_url,
            'is_peer_reviewed' => $a->is_peer_reviewed,
            'read_time_minutes' => $a->read_time_minutes,
            'published_at' => $a->published_at,
        ]);

        return Inertia::render('Marketplace/Journal/Index', [
            'articles' => $articles,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'search' => $request->input('search', ''),
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
        if (! $article) {
            $article = JournalArticle::latest('published_at')->first();
        }

        $recentArticles = JournalArticle::where('id', '!=', $article?->id)
            ->latest('published_at')
            ->limit(4)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'slug' => $a->slug,
                'hero_image_url' => $a->hero_image_url,
                'read_time_minutes' => $a->read_time_minutes,
            ]);

        return Inertia::render('Marketplace/Journal/Show', [
            'article' => $article,
            'recentArticles' => $recentArticles,
        ]);
    }

    public function notFound()
    {
        return Inertia::render('Marketplace/Errors/NotFound')->toResponse(request())->setStatusCode(404);
    }

    public function arViewer(Product $product)
    {
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
                'categories' => $product->categories->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                ]),
                'store' => $product->store ? [
                    'id' => $product->store->id,
                    'name' => $product->store->name,
                ] : null,
            ],
            'modelUrl' => $product->model_3d_url
                ? (str_starts_with($product->model_3d_url, 'http') ? $product->model_3d_url : '/storage/'.ltrim($product->model_3d_url, '/'))
                : '/storage/models/bonsai.glb',
        ]);
    }
}
