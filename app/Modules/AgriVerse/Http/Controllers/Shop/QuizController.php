<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Product;
use Illuminate\Http\Request;

class QuizController
{
    private function normalize(string $str): string
    {
        $str = strtolower($str);
        $normalized = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);

        return $normalized === false ? $str : $normalized;
    }

    public function recommend(Request $request)
    {
        $validated = $request->validate([
            'light' => 'required|string',
            'humidity' => 'required|string',
            'level' => 'required|string',
        ]);

        $light = $this->normalize($validated['light']);
        $humidity = $this->normalize($validated['humidity']);
        $level = $this->normalize($validated['level']);

        $products = Product::published()
            ->withCount(['orders as sold_count' => fn ($q) => $q->whereIn('status', ['completed', 'delivered'])])
            ->get()
            ->filter(fn ($p) => $p->stock > 0)
            ->map(function ($p) use ($light, $humidity, $level) {
                $spec = is_array($p->technical_specs) ? $p->technical_specs : [];
                $lightSpec = $this->normalize($spec['Ánh sáng'] ?? '');

                $score = 0;

                if ($this->contains($light, 'truc tiep') || $this->contains($light, 'lọc') || $this->contains($light, 'luong')) {
                    $score += $this->matchLight($light, $lightSpec);
                }

                if ($this->contains($humidity, 'cao')) {
                    if (str_contains($lightSpec, 'am') || str_contains($lightSpec, 'nuoc')) {
                        $score += 3;
                    }
                    if (str_contains($this->normalize($spec['Tưới nước'] ?? ''), 'am') || str_contains($this->normalize($spec['Tưới nước'] ?? ''), 'phun')) {
                        $score += 2;
                    }
                } else {
                    if (str_contains($lightSpec, 'thieu sang')) {
                        $score += 2;
                    }
                    if ($this->contains($this->normalize($spec['Tưới nước'] ?? ''), 'khan')) {
                        $score += 2;
                    }
                }

                if ($this->contains($level, 'dam me')) {
                    $score += $p->price < 200000 ? 3 : ($p->price < 400000 ? 1 : 0);
                } elseif ($this->contains($level, 'tho san')) {
                    $score += $p->price >= 400000 ? 3 : ($p->price >= 200000 ? 1 : 0);
                } else {
                    $score += $p->price >= 150000 && $p->price <= 500000 ? 2 : 1;
                }

                return ['product' => $p, 'score' => $score];
            })
            ->sortByDesc('score')
            ->take(6);

        $result = $products->map(function ($item) {
            $p = $item['product'];
            $spec = is_array($p->technical_specs) ? $p->technical_specs : [];

            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->price,
                'compare_price' => $p->compare_price,
                'image' => $p->image,
                'category' => $p->category,
                'sold_count' => (int) $p->sold_count,
                'model_3d_path' => $p->model_3d_path,
                'light_need' => $spec['Ánh sáng'] ?? null,
                'watering' => $spec['Tưới nước'] ?? null,
            ];
        })->values();

        return response()->json(['products' => $result]);
    }

    private function contains(string $haystack, string $needle): bool
    {
        return str_contains($haystack, $this->normalize($needle));
    }

    private function matchLight(string $light, string $spec): int
    {
        $isDirect = str_contains($light, 'truc tiep');
        $isBright = str_contains($light, 'loc') || str_contains($light, 'gián tiep');
        $isLow = str_contains($light, 'yeu') || str_contains($light, 'thap');

        $specDirect = $this->contains($spec, 'nang') || $this->contains($spec, 'truc tiep');
        $specBright = $this->contains($spec, 'gián tiep') || $this->contains($spec, 'ban ram') || $this->contains($spec, 'sang');
        $specLow = $this->contains($spec, 'bong') || $this->contains($spec, 'it sang') || $this->contains($spec, 'thieu sang');

        if ($isDirect && $specDirect) {
            return 5;
        }
        if ($isLow && $specLow) {
            return 5;
        }
        if ($isBright && $specBright) {
            return 5;
        }

        return 0;
    }
}