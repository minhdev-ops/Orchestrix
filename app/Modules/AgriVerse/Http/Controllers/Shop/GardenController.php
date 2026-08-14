<?php
namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\GardenPlant;
use App\Modules\AgriVerse\Services\GardenService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class GardenController extends Controller
{
    public function __construct(
        private readonly GardenService $gardenService,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $data = $this->gardenService->getGardenData($user);
        $suggestions = $this->gardenService->getSuggestedProducts($data['garden']);

        return Inertia::render('Marketplace/Garden/Index', [
            ...$data,
            'suggestions' => $suggestions,
        ]);
    }

    public function water(Request $request, GardenPlant $plant)
    {
        if ($plant->garden->user_id !== $request->user()->id) {
            abort(403);
        }

        $this->gardenService->waterPlant($plant);

        return back()->with('success', 'Đã tưới cây thành công!');
    }

    public function fertilize(Request $request, GardenPlant $plant)
    {
        if ($plant->garden->user_id !== $request->user()->id) {
            abort(403);
        }

        $this->gardenService->fertilizePlant($plant);

        return back()->with('success', 'Đã bón phân thành công!');
    }

    public function move(Request $request, GardenPlant $plant)
    {
        if ($plant->garden->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'zone_id' => 'required|exists:garden_zones,id',
            'position_x' => 'integer|min:0',
            'position_y' => 'integer|min:0',
        ]);

        $this->gardenService->movePlant($plant, $validated['zone_id'], $validated['position_x'] ?? 0, $validated['position_y'] ?? 0);

        return back()->with('success', 'Đã di chuyển cây!');
    }

    public function updateStage(Request $request, GardenPlant $plant)
    {
        if ($plant->garden->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'stage' => 'required|in:seedling,growing,mature,flowering,fruiting,harvested',
        ]);

        $this->gardenService->updateStage($plant, $validated['stage']);

        return back()->with('success', 'Đã cập nhật giai đoạn phát triển!');
    }

    public function destroy(Request $request, GardenPlant $plant)
    {
        if ($plant->garden->user_id !== $request->user()->id) {
            abort(403);
        }

        $this->gardenService->removePlant($plant);

        return back()->with('success', 'Đã xóa cây khỏi vườn!');
    }
}
