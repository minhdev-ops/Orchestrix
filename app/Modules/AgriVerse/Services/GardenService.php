<?php
namespace App\Modules\AgriVerse\Services;

use App\Models\User;
use App\Modules\AgriVerse\Models\Garden;
use App\Modules\AgriVerse\Models\GardenZone;
use App\Modules\AgriVerse\Models\GardenPlant;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GardenService
{
    private const PLANT_CATEGORIES = [
        'bonsai-co-thu',
        'cay-canh-mini',
        'sen-da-xuong-rong',
        'cay-thuy-sinh',
        'cay-an-qua-bonsai',
    ];

    private const STAGE_ORDER = ['seedling', 'growing', 'mature', 'flowering', 'fruiting', 'harvested'];

    public function getOrCreateGarden(User $user): Garden
    {
        return Garden::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => "Vườn của {$user->name}",
                'health_score' => 50,
                'grade' => 'C',
            ]
        );
    }

    public function ensureDefaultZones(Garden $garden): void
    {
        $defaultZones = [
            ['name' => 'Rau', 'icon' => '🥬', 'sort_order' => 1],
            ['name' => 'Hoa', 'icon' => '🌸', 'sort_order' => 2],
            ['name' => 'Cây ăn quả', 'icon' => '🍊', 'sort_order' => 3],
            ['name' => 'Cây cảnh', 'icon' => '🌿', 'sort_order' => 4],
            ['name' => 'Thủy canh', 'icon' => '💧', 'sort_order' => 5],
        ];

        foreach ($defaultZones as $zone) {
            $garden->zones()->firstOrCreate(
                ['name' => $zone['name']],
                $zone
            );
        }
    }

    public function getGardenData(User $user): array
    {
        $garden = $this->getOrCreateGarden($user);
        $this->ensureDefaultZones($garden);

        $plants = $garden->plants()->with('zone', 'product')->latest('planted_at')->get();
        $zones = $garden->zones()->orderBy('sort_order')->get();

        foreach ($zones as $zone) {
            $zone->plant_count = $plants->where('zone_id', $zone->id)->count();
            $zone->save();
        }

        $totalPlants = $plants->count();
        $avgHydration = $plants->avg('hydration_value') ?? 0;
        $avgNutrient = $plants->avg('nutrient_value') ?? 0;
        $healthyCount = $plants->where('health_status', 'healthy')->count();
        $healthScore = $totalPlants > 0 ? round(($avgHydration * 0.4 + $avgNutrient * 0.3 + ($healthyCount / $totalPlants) * 100 * 0.3)) : 0;
        $grade = $this->calculateGrade($healthScore);

        $garden->update([
            'total_plants' => $totalPlants,
            'health_score' => $healthScore,
            'grade' => $grade,
        ]);

        $stageDistribution = [];
        foreach (self::STAGE_ORDER as $stage) {
            $count = $plants->where('stage', $stage)->count();
            if ($count > 0) {
                $stageDistribution[$stage] = $count;
            }
        }

        $careNeeded = $plants->filter(fn ($p) =>
            $p->hydration_value < 30 || $p->nutrient_value < 30 || $p->health_status !== 'healthy'
        )->values();

        return [
            'garden' => $garden,
            'zones' => $zones,
            'plants' => $plants,
            'stats' => [
                'totalPlants' => $totalPlants,
                'healthScore' => $healthScore,
                'grade' => $grade,
                'avgHydration' => round($avgHydration),
                'avgNutrient' => round($avgNutrient),
                'healthyCount' => $healthyCount,
                'careNeededCount' => $careNeeded->count(),
            ],
            'stageDistribution' => $stageDistribution,
            'careNeeded' => $careNeeded,
        ];
    }

    public function addPlantFromOrder(Order $order): ?GardenPlant
    {
        if (! $this->isPlantProduct($order->product)) {
            return null;
        }

        $user = $order->buyer;
        $garden = $this->getOrCreateGarden($user);
        $this->ensureDefaultZones($garden);

        $zone = $garden->zones()->where('name', 'Cây cảnh')->first() ?? $garden->zones()->first();

        return $garden->plants()->create([
            'zone_id' => $zone->id,
            'product_id' => $order->product_id,
            'order_id' => $order->id,
            'name' => $order->product->name,
            'species' => $order->product->name,
            'image_url' => $order->product->image,
            'stage' => 'seedling',
            'planted_at' => now(),
            'hydration_value' => 80,
            'nutrient_value' => 50,
            'health_status' => 'healthy',
        ]);
    }

    public function isPlantProduct(Product $product): bool
    {
        $categorySlugs = $product->categories->pluck('slug')->toArray();
        return ! empty(array_intersect($categorySlugs, self::PLANT_CATEGORIES));
    }

    public function waterPlant(GardenPlant $plant): void
    {
        $plant->update([
            'hydration_value' => min(100, $plant->hydration_value + 20),
            'last_watered_at' => now(),
            'health_status' => $plant->hydration_value + 20 >= 30 ? 'healthy' : $plant->health_status,
        ]);
    }

    public function fertilizePlant(GardenPlant $plant): void
    {
        $plant->update([
            'nutrient_value' => min(100, $plant->nutrient_value + 15),
            'last_fertilized_at' => now(),
            'health_status' => $plant->nutrient_value + 15 >= 30 ? 'healthy' : $plant->health_status,
        ]);
    }

    public function movePlant(GardenPlant $plant, int $zoneId, int $positionX, int $positionY): void
    {
        $plant->update([
            'zone_id' => $zoneId,
            'position_x' => $positionX,
            'position_y' => $positionY,
        ]);
    }

    public function updateStage(GardenPlant $plant, string $stage): void
    {
        if (! in_array($stage, self::STAGE_ORDER)) {
            throw new \InvalidArgumentException("Invalid stage: {$stage}");
        }
        $plant->update(['stage' => $stage]);
    }

    public function removePlant(GardenPlant $plant): void
    {
        $plant->delete();
    }

    public function calculateGrade(int $score): string
    {
        return match (true) {
            $score >= 85 => 'A+',
            $score >= 70 => 'A',
            $score >= 50 => 'B',
            $score >= 30 => 'C',
            default => 'D',
        };
    }

    public function getSuggestedProducts(Garden $garden): Collection
    {
        $existingPlantIds = $garden->plants()->pluck('product_id')->filter()->values();

        return Product::published()
            ->whereHas('categories', fn ($q) => $q->whereIn('slug', self::PLANT_CATEGORIES))
            ->whereNotIn('id', $existingPlantIds)
            ->inRandomOrder()
            ->limit(6)
            ->get();
    }
}
