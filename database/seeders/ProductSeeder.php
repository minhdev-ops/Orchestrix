<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\ProductImage;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\TreeSpecies;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private const IMAGE_DIR = 'userfiles/images/products';

    public function run(): void
    {
        $user = User::where('email', 'seller@orchestrix.com')->first();
        $store = Store::where('owner_id', $user?->id)->first();

        if (! $user || ! $store) {
            $this->command->error('Không tìm thấy seller user hoặc store!');

            return;
        }

        // Ensure CKFinder directory exists
        $ckfinderDir = public_path(self::IMAGE_DIR);
        if (! is_dir($ckfinderDir)) {
            mkdir($ckfinderDir, 0755, true);
        }

        $species = TreeSpecies::all();

        if ($species->isEmpty()) {
            $this->command->error('Không có dữ liệu tree_species!');

            return;
        }

        $count = 0;
        $imageCount = 0;


        foreach ($species as $item) {
            $categorySlug = $this->mapCategory($item->category_type);

            $product = Product::firstOrCreate(
                ['name' => $item->species_vn],
                [
                    'user_id' => $user->id,
                    'store_id' => $store->id,
                    'slug' => Str::slug($item->species_vn).'-'.Str::random(6),
                    'description' => $this->buildDescription($item),
                    'price' => $this->estimatePrice($item),
                    'stock' => rand(5, 50),
                    'category' => $categorySlug,
                    'tags' => $this->buildTags($item),
                    'status' => 'published',
                    'technical_specs' => $this->buildSpecs($item),
                    'metadata' => [
                        'source_url' => $item->source_url,
                        'species_latin' => $item->species_latin,
                        'family' => $item->family,
                        'origin' => $item->origin,
                        'light_requirements' => $item->light_requirements,
                        'watering_needs' => $item->watering_needs,
                        'fertilizing_guide' => $item->fertilizing_guide,
                        'meaning_fengshui' => $item->meaning_fengshui,
                    ],
                ]
            );

            // Download images to CKFinder directory
            $firstImagePath = null;
            if ($item->image_urls) {
                $images = is_array($item->image_urls) ? $item->image_urls : json_decode($item->image_urls, true) ?? [];
                $sortOrder = 0;
                foreach ($images as $url) {
                    $url = trim($url);
                    if (empty($url)) {
                        continue;
                    }

                    $imagePath = $this->downloadImage($url, $product->id, $sortOrder === 0);
                    if ($imagePath) {
                        if ($firstImagePath === null) {
                            $firstImagePath = $imagePath;
                        }
                        ProductImage::firstOrCreate(
                            ['product_id' => $product->id, 'path' => $imagePath],
                            [
                                'sort_order' => $sortOrder,
                                'is_primary' => $sortOrder === 0,
                                'alt_text' => $item->species_vn,
                            ]
                        );
                        $sortOrder++;
                        $imageCount++;
                    }
                }
            }

            // If no image was downloaded, create a placeholder
            if ($firstImagePath === null) {
                $firstImagePath = $this->createPlaceholderImage($product->id, $item->species_vn);
            }

            // CRITICAL: Set the `image` field on Product so frontend shows it
            // Store /storage/... path; works via storage symlink (public/storage -> storage/app/public)
            // and symlink storage/app/public/userfiles -> public/userfiles
            if ($firstImagePath) {
                $product->update(['image' => '/storage/'.$firstImagePath]);
            }

            $count++;
        }

        $this->command->info("✅ Đã tạo $count sản phẩm từ dữ liệu tree_species!");
        $this->command->info("📸 Đã tải $imageCount ảnh về CKFinder (public/userfiles/images/products/)!");
    }

    private function createPlaceholderImage(int $productId, string $name): string
    {
        // Generate a colored placeholder with the plant name
        $hash = crc32($name);
        $hue = abs($hash) % 360;

        // Create JPG placeholder using GD
        $width = 400;
        $height = 400;
        $img = imagecreatetruecolor($width, $height);

        // Convert HSL to RGB for GD
        $bgRgb = $this->hslToRgb($hue / 360, 0.55, 0.65);
        $bgColor = imagecolorallocate($img, $bgRgb[0], $bgRgb[1], $bgRgb[2]);
        $textColor = imagecolorallocate($img, 255, 255, 255);

        // Fill background
        imagefilledrectangle($img, 0, 0, $width, $height, $bgColor);

        // Add text - get first 2 characters
        $shortName = mb_substr($name, 0, 2, 'UTF-8');
        $label = 'cây cảnh';

        // Use built-in font (5 is the largest built-in GD font, ~16px)
        $shortBox = imagettfbbox(60, 0, public_path('fonts/DejaVuSans-Bold.ttf'), $shortName);
        if ($shortBox === false) {
            // Fallback: use built-in font if TTF not available
            $shortX = ($width - imagefontwidth(5) * strlen($shortName)) / 2;
            imagestring($img, 5, (int) $shortX, 70, $shortName, $textColor);
        } else {
            $shortX = ($width - ($shortBox[2] - $shortBox[0])) / 2;
            $shortY = ($height / 2 - 30) - ($shortBox[7] - $shortBox[1]) / 2;
            imagettftext($img, 60, 0, (int) $shortX, (int) $shortY, $textColor, public_path('fonts/DejaVuSans-Bold.ttf'), $shortName);
        }

        $labelBox = imagettfbbox(18, 0, public_path('fonts/DejaVuSans.ttf'), $label);
        if ($labelBox === false) {
            $labelX = ($width - imagefontwidth(5) * strlen($label)) / 2;
            imagestring($img, 5, (int) $labelX, 210, $label, $textColor);
        } else {
            $labelX = ($width - ($labelBox[2] - $labelBox[0])) / 2;
            $labelY = ($height / 2 + 30) - ($labelBox[7] - $labelBox[1]) / 2;
            imagettftext($img, 18, 0, (int) $labelX, (int) $labelY, $textColor, public_path('fonts/DejaVuSans.ttf'), $label);
        }

        $filename = $productId.'_placeholder.jpg';
        $fullPath = public_path(self::IMAGE_DIR.'/'.$filename);
        imagejpeg($img, $fullPath, 90);
        imagedestroy($img);

        return self::IMAGE_DIR.'/'.$filename;
    }

    /**
     * Convert HSL to RGB (values 0-1) returns [R, G, B] 0-255
     */
    private function hslToRgb(float $h, float $s, float $l): array
    {
        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $this->hueToRgb($p, $q, $h + 1 / 3);
            $g = $this->hueToRgb($p, $q, $h);
            $b = $this->hueToRgb($p, $q, $h - 1 / 3);
        }

        return [(int) round($r * 255), (int) round($g * 255), (int) round($b * 255)];
    }

    private function hueToRgb(float $p, float $q, float $t): float
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1 / 2) return $q;
        if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;
        return $p;
    }

    private function downloadImage(string $url, int $productId, bool $isPrimary): ?string
    {
        // Handle relative URLs
        if (str_starts_with($url, '/')) {
            $url = 'https://www.bonsaiempire.vn'.$url;
        }

        if (! str_starts_with($url, 'http')) {
            return null;
        }

        try {
            $response = Http::timeout(15)->get($url);
            if (! $response->successful()) {
                return null;
            }

            $content = $response->body();
            $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $productId.($isPrimary ? '_main' : '_'.uniqid()).'.'.$ext;

            // Save directly to CKFinder directory (public/userfiles/images/products/)
            // Accessible via /storage/userfiles/images/products/ thanks to symlink:
            // public/storage -> storage/app/public AND storage/app/public/userfiles -> public/userfiles
            $fullPath = public_path(self::IMAGE_DIR.'/'.$filename);
            file_put_contents($fullPath, $content);

            return self::IMAGE_DIR.'/'.$filename;
        } catch (\Exception $e) {
            $this->command->warn("  ⚠️ Không thể tải ảnh: $url - ".$e->getMessage());

            return null;
        }
    }

    private function buildDescription(TreeSpecies $item): string
    {
        $parts = [];

        if ($item->species_latin) {
            $parts[] = "🌿 *Tên khoa học:* {$item->species_latin}";
        }
        if ($item->family) {
            $parts[] = "🏷️ *Họ:* {$item->family}";
        }
        if ($item->origin) {
            $parts[] = "🌍 *Nguồn gốc:* {$item->origin}";
        }

        $parts[] = '';
        $parts[] = '---';
        $parts[] = '**📋 Hướng dẫn chăm sóc:**';
        $parts[] = '';

        if ($item->light_requirements) {
            $parts[] = "☀️ *Ánh sáng:* {$item->light_requirements}";
        }
        if ($item->watering_needs) {
            $parts[] = "💧 *Tưới nước:* {$item->watering_needs}";
        }
        if ($item->fertilizing_guide) {
            $parts[] = "🧪 *Phân bón:* {$item->fertilizing_guide}";
        }
        if ($item->min_temp) {
            $parts[] = "🌡️ *Nhiệt độ tối thiểu:* {$item->min_temp}°C";
        }
        if ($item->soil_ph) {
            $parts[] = "🧫 *Độ pH đất:* {$item->soil_ph}";
        }
        if ($item->indoor_outdoor) {
            $parts[] = "🏠 *Vị trí:* {$item->indoor_outdoor}";
        }

        if ($item->key_features) {
            $parts[] = '';
            $parts[] = '---';
            $parts[] = "**📝 Mô tả:** {$item->key_features}";
        }

        if ($item->meaning_fengshui) {
            $parts[] = '';
            $parts[] = "💰 *Ý nghĩa phong thủy:* {$item->meaning_fengshui}";
        }

        return implode("\n", $parts);
    }

    private function estimatePrice(TreeSpecies $item): float
    {
        $category = $item->category_type ?? '';
        if (str_contains($category, 'Bonsai') || str_contains($category, 'cổ thụ')) {
            return rand(200000, 8000000);
        }
        if (str_contains($category, 'nội thất') || str_contains($category, 'phong thủy')) {
            return rand(100000, 2000000);
        }
        if (str_contains($category, 'mini') || str_contains($category, 'để bàn')) {
            return rand(50000, 500000);
        }
        if (str_contains($category, 'sen đá')) {
            return rand(30000, 300000);
        }
        if (str_contains($category, 'Tết')) {
            return rand(200000, 5000000);
        }

        return rand(100000, 3000000);
    }

    private function mapCategory(?string $categoryType): string
    {
        if (! $categoryType) {
            return 'cay-canh-mini';
        }

        $lower = mb_strtolower($categoryType, 'UTF-8');

        if (str_contains($lower, 'bonsai') || str_contains($lower, 'cổ thụ')) {
            return 'bonsai-co-thu';
        }
        if (str_contains($lower, 'nội thất') || str_contains($lower, 'phong thủy')) {
            return 'cay-canh-mini';
        }
        if (str_contains($lower, 'sen đá') || str_contains($lower, 'xương rồng')) {
            return 'sen-da-xuong-rong';
        }
        if (str_contains($lower, 'thủy sinh')) {
            return 'cay-thuy-sinh';
        }
        if (str_contains($lower, 'ăn quả') || str_contains($lower, 'tết')) {
            return 'cay-an-qua-bonsai';
        }
        if (str_contains($lower, 'bụi') || str_contains($lower, 'sân vườn')) {
            return 'bonsai-co-thu';
        }

        return 'cay-canh-mini';
    }

    private function buildTags(TreeSpecies $item): array
    {
        $tags = [];
        if ($item->species_latin) {
            $tags[] = $item->species_latin;
        }
        if ($item->category_type) {
            $tags[] = $item->category_type;
        }
        if ($item->flower_color && $item->flower_color !== '-' && $item->flower_color !== '') {
            $tags[] = 'hoa-'.$item->flower_color;
        }
        if ($item->meaning_fengshui) {
            $tags[] = 'phong-thuy';
        }
        $tags[] = 'cay-canh';

        return array_unique($tags);
    }

    private function buildSpecs(TreeSpecies $item): array
    {
        $specs = [];
        if ($item->species_latin) {
            $specs['Tên khoa học'] = $item->species_latin;
        }
        if ($item->family) {
            $specs['Họ thực vật'] = $item->family;
        }
        if ($item->light_requirements) {
            $specs['Ánh sáng'] = $item->light_requirements;
        }
        if ($item->watering_needs) {
            $specs['Tưới nước'] = $item->watering_needs;
        }
        if ($item->min_temp) {
            $specs['Nhiệt độ tối thiểu'] = $item->min_temp.'°C';
        }
        if ($item->soil_ph) {
            $specs['Độ pH đất'] = $item->soil_ph;
        }
        if ($item->flower_color && $item->flower_color !== '-') {
            $specs['Màu hoa'] = $item->flower_color;
        }
        if ($item->bloom_season && $item->bloom_season !== '-') {
            $specs['Mùa hoa'] = $item->bloom_season;
        }

        return $specs;
    }
}
