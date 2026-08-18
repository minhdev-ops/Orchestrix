<?php

namespace Database\Seeders;

use App\Modules\AgriVerse\Models\TreeSpecies;
use Illuminate\Database\Seeder;

class VietnamesePlantSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('seeders/data/vietnamese_plants.csv');

        if (! file_exists($csvPath)) {
            $source = base_path('docs/vietnamese_plants_comprehensive.csv');
            if (file_exists($source)) {
                if (! is_dir(database_path('seeders/data'))) {
                    mkdir(database_path('seeders/data'), 0755, true);
                }
                copy($source, $csvPath);
            } else {
                $this->command->error('Không tìm thấy file vietnamese_plants_comprehensive.csv!');

                return;
            }
        }

        $rows = array_map('str_getcsv', file($csvPath));
        $header = array_shift($rows);

        if (! $header) {
            $this->command->error('File CSV rỗng!');

            return;
        }

        // Strip BOM
        $header[0] = preg_replace('/^\\xEF\\xBB\\xBF/', '', $header[0]);

        $count = 0;
        foreach ($rows as $row) {
            if (count($row) < 2) {
                continue;
            }

            $data = array_combine($header, $row);
            if ($data === false) {
                continue;
            }

            $species = [
                'species_vn' => $data['species_vn'] ?? '',
                'species_latin' => $data['species_latin'] ?? null,
                'family' => $data['family'] ?? null,
                'category_type' => $data['category_type'] ?? null,
                'type' => $data['type'] ?? null,
                'origin' => $data['origin'] ?? null,
                'max_height' => null,
                'light_requirements' => $data['light_requirements'] ?? null,
                'min_temp' => isset($data['min_temp']) && $data['min_temp'] !== '' && $data['min_temp'] !== '-1' ? (int) $data['min_temp'] : null,
                'watering_needs' => $data['watering_needs'] ?? null,
                'soil_ph' => $data['soil_ph'] ?? null,
                'repotting_freq' => null,
                'propagation_methods' => null,
                'common_pests' => null,
                'flower_color' => $data['flower_color'] ?? null,
                'bloom_season' => $data['bloom_season'] ?? null,
                'fertilizing_guide' => $data['fertilizing_guide'] ?? null,
                'pruning_wiring' => null,
                'indoor_outdoor' => $data['indoor_outdoor'] ?? null,
                'meaning_fengshui' => $data['meaning_fengshui'] ?? null,
                'key_features' => $data['key_features'] ?? null,
                'description' => $data['key_features'] ?? null,
                'image_urls' => isset($data['image_url']) && $data['image_url'] ? [$data['image_url']] : null,
                'source_url' => $data['source_url'] ?? null,
            ];

            TreeSpecies::create($species);
            $count++;
        }

        $this->command->info("✅ Đã import $count loài cây cảnh Việt Nam vào database!");
    }
}
