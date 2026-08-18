<?php

namespace Database\Seeders;

use App\Modules\AgriVerse\Models\TreeSpecies;
use Illuminate\Database\Seeder;

class TreeSpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('seeders/data/tree_species.csv');

        if (! file_exists($csvPath)) {
            $this->command->warn("⚠️  Không tìm thấy file CSV: $csvPath");
            $this->command->warn("Đang copy từ docs/bonsaiempire_tree_species.csv...");

            $source = base_path('docs/bonsaiempire_tree_species.csv');
            if (file_exists($source)) {
                if (! is_dir(database_path('seeders/data'))) {
                    mkdir(database_path('seeders/data'), 0755, true);
                }
                copy($source, $csvPath);
            } else {
                $this->command->error('Không tìm thấy file CSV nguồn!');

                return;
            }
        }

        $rows = array_map('str_getcsv', file($csvPath));
        $header = array_shift($rows);

        if (! $header) {
            $this->command->error('File CSV rỗng hoặc không đúng định dạng!');

            return;
        }

        // Strip BOM from first header if present
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

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
                'category_type' => $data['category'] ?? null,
                'type' => $data['type'] ?? null,
                'origin' => $data['origin'] ?? null,
                'max_height' => isset($data['max_height_m']) && $data['max_height_m'] !== '-1' && $data['max_height_m'] !== '' ? (int) $data['max_height_m'] : null,
                'light_requirements' => $data['light'] ?? null,
                'min_temp' => isset($data['min_temp_c']) && $data['min_temp_c'] !== '-1' && $data['min_temp_c'] !== '' ? (int) $data['min_temp_c'] : null,
                'watering_needs' => $data['watering'] ?? null,
                'soil_ph' => $data['soil_ph'] ?? null,
                'repotting_freq' => $data['repotting_freq_years'] ?? null,
                'propagation_methods' => $data['propagation'] ?? null,
                'common_pests' => $data['common_pests'] ?? null,
                'flower_color' => $data['flower_color'] ?? null,
                'bloom_season' => $data['bloom_season'] ?? null,
                'fertilizing_guide' => $data['fertilizing'] ?? null,
                'pruning_wiring' => $data['pruning_wiring'] ?? null,
                'indoor_outdoor' => $data['indoor_outdoor'] ?? null,
                'meaning_fengshui' => $data['meaning_fengshui'] ?? null,
                'key_features' => $data['key_features'] ?? null,
                'image_urls' => isset($data['image_urls']) && $data['image_urls'] ? explode(',', $data['image_urls']) : null,
                'source_url' => $data['care_guide_url'] ?? null,
            ];

            TreeSpecies::create($species);
            $count++;
        }

        $this->command->info("✅ Đã import $count loài cây bonsai vào database!");
    }
}
