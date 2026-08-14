<?php

namespace Database\Seeders;

use App\Modules\AgriVerse\Models\CareGuide;
use Illuminate\Database\Seeder;

class CareGuideSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('seeders/data/care_guides.csv');

        if (! file_exists($csvPath)) {
            $source = base_path('docs/bonsaiempire_care_guides.csv');
            if (file_exists($source)) {
                if (! is_dir(database_path('seeders/data'))) {
                    mkdir(database_path('seeders/data'), 0755, true);
                }
                copy($source, $csvPath);
            } else {
                $this->command->error('Không tìm thấy file care_guides CSV!');

                return;
            }
        }

        $rows = array_map('str_getcsv', file($csvPath));
        $header = array_shift($rows);

        if (! $header) {
            $this->command->error('File CSV rỗng!');

            return;
        }

        $count = 0;
        foreach ($rows as $row) {
            if (count($row) < 2) {
                continue;
            }

            $data = array_combine($header, $row);
            if ($data === false) {
                continue;
            }

            CareGuide::create([
                'guide_topic' => $data['guide_topic'] ?? '',
                'guide_url' => $data['guide_url'] ?? null,
                'vietnamese_title' => $data['vietnamese_title'] ?? null,
                'key_content' => $data['key_content'] ?? '',
            ]);
            $count++;
        }

        $this->command->info("✅ Đã import $count hướng dẫn chăm sóc vào database!");
    }
}
