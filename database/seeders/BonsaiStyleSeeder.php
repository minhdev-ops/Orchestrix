<?php

namespace Database\Seeders;

use App\Modules\AgriVerse\Models\BonsaiStyle;
use Illuminate\Database\Seeder;

class BonsaiStyleSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('seeders/data/blog_styles.csv');

        if (! file_exists($csvPath)) {
            $source = base_path('docs/bonsaiempire_blog_styles.csv');
            if (file_exists($source)) {
                if (! is_dir(database_path('seeders/data'))) {
                    mkdir(database_path('seeders/data'), 0755, true);
                }
                copy($source, $csvPath);
            } else {
                $this->command->error('Không tìm thấy file blog_styles CSV!');

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

            $section = $data['section'] ?? '';

            // Only seed bonsai styles (skip blog/gallery for now)
            if ($section === 'Bonsai Styles') {
                $name = $data['title'] ?? '';

                // Parse "Chokkan (Dáng trực)" style names
                $nameJp = null;
                $nameVn = $name;
                if (preg_match('/^(.+?)\s*\((.+?)\)$/', $name, $m)) {
                    $nameJp = trim($m[1]);
                    $nameVn = trim($m[2]);
                }

                BonsaiStyle::create([
                    'name_vn' => $nameVn,
                    'name_jp' => $nameJp,
                    'description' => $data['content'] ?? '',
                    'section' => $section,
                ]);
                $count++;
            }
        }

        $this->command->info("✅ Đã import $count thế bonsai vào database!");
    }
}
