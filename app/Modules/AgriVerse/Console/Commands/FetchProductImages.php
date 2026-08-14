<?php

namespace App\Modules\AgriVerse\Console\Commands;

use App\Modules\AgriVerse\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FetchProductImages extends Command
{
    protected $signature = 'agriverse:fetch-product-images';
    protected $description = 'Tải ảnh thật từ Wikipedia cho các sản phẩm đang dùng ảnh placeholder';

    private array $searchTerms = [
        1  => 'Ficus microcarpa',
        2  => 'Juniperus chinensis',
        3  => 'Podocarpus macrophyllus',
        4  => 'Bougainvillea',
        5  => 'Wrightia religiosa',
        6  => 'Ochna integerrima',
        7  => 'Ixora coccinea',
        8  => 'Serissa japonica',
        9  => 'Zamioculcas zamiifolia',
        10 => 'Epipremnum aureum',
        11 => 'Sansevieria trifasciata',
        12 => 'Epipremnum aureum',
        13 => 'Dracaena fragrans',
        14 => 'Dieffenbachia seguine',
        15 => 'Aglaonema commutatum',
        16 => 'Spathiphyllum wallisii',
        17 => 'Aglaonema nitidum',
        18 => 'Syngonium podophyllum',
        19 => 'Chamaedorea elegans',
        20 => 'Polyscias fruticosa',
        21 => 'Aglaonema rotundum',
        22 => 'Dracaena fragrans',
        23 => 'Schefflera heptaphylla',
        24 => 'Ficus lyrata',
        25 => 'Monstera deliciosa',
        26 => 'Ficus elastica',
        27 => 'Anthurium andraeanum',
        28 => 'Peperomia obtusifolia',
        29 => 'Epipremnum aureum',
        30 => 'Fern',
        31 => 'Fittonia albivenis',
        32 => 'Calathea crocata',
        33 => 'Epipremnum aureum',
        34 => 'Philodendron erubescens',
        35 => 'Philodendron hastatum',
        36 => 'Dracaena sanderiana',
        37 => 'Pilea peperomioides',
        38 => 'Philodendron erubescens',
        39 => 'Clivia miniata',
        40 => 'Succulent',
        41 => 'Cactus',
        42 => 'Juniperus chinensis',
        43 => 'Ficus microcarpa',
        44 => 'Ochna integerrima',
        45 => 'Bougainvillea',
        46 => 'Wrightia religiosa',
        47 => 'Serissa japonica',
        48 => 'Podocarpus macrophyllus',
        49 => 'Ixora coccinea',
        50 => 'Monstera obliqua',
        51 => 'Podocarpus macrophyllus',
        52 => 'Polyscias fruticosa',
        53 => 'Cycas revoluta',
        54 => 'Adenium obesum',
        55 => 'Barringtonia acutangula',
        56 => 'Ficus carica',
        57 => 'Citrus japonica',
        58 => 'Prunus persica',
        59 => 'Psidium guajava',
        60 => 'Averrhoa carambola',
    ];

    public function handle()
    {
        $products = Product::whereIn('id', array_keys($this->searchTerms))->get();
        $this->info("Found {$products->count()} products to fetch images for");

        $imported = 0;
        $failed = 0;

        foreach ($products as $product) {
            $term = $this->searchTerms[$product->id] ?? $product->name;
            $this->line("  [{$product->id}] {$product->name} -> '{$term}'...");

            $imageUrl = $this->fetchImage($term);

            if (!$imageUrl) {
                $this->warn("    No image found for '{$term}'");
                $failed++;
                continue;
            }

            $filename = $product->id . '_main.jpg';
            $path = 'userfiles/images/products/' . $filename;

            try {
                $response = Http::timeout(15)
                    ->withHeaders(['User-Agent' => 'AgriVerse/1.0 (https://agriverse.vn)'])
                    ->get($imageUrl);

                if ($response->successful() && $response->body()) {
                    Storage::disk('public')->put($path, $response->body());
                    $product->update(['image' => '/storage/' . $path]);
                    $this->info("    ✓ {$filename}");
                    $imported++;
                } else {
                    $this->warn("    HTTP {$response->status()} downloading image");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->warn("    Error: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Done! Imported: {$imported}, Failed: {$failed}");
    }

    private function fetchImage(string $term): ?string
    {
        $titles = [$term, $term . ' plant', $term . ' tree', $term . ' houseplant'];
        foreach ($titles as $title) {
            $result = $this->fetchFromWikipedia($title);
            if ($result) return $result;
        }
        return null;
    }

    private function fetchFromWikipedia(string $title): ?string
    {
        try {
            $url = 'https://en.wikipedia.org/api/rest_v1/page/summary/' . rawurlencode($title);
            $resp = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'AgriVerse/1.0 (https://agriverse.vn)'])
                ->get($url);

            if (!$resp->successful()) return null;

            $data = $resp->json();
            if (!isset($data['thumbnail']['source'])) return null;

            if (isset($data['originalimage']['source'])) {
                return $data['originalimage']['source'];
            }

            $img = $data['thumbnail']['source'];
            if (!str_contains($img, 'upload.wikimedia.org')) return null;

            return $img;
        } catch (\Exception $e) {
            $this->warn("    API error: {$e->getMessage()}");
            return null;
        }
    }
}
