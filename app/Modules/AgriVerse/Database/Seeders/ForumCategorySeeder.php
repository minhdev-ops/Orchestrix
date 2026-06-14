<?php

namespace App\Modules\AgriVerse\Database\Seeders;

use App\Modules\AgriVerse\Models\ForumCategory;
use Illuminate\Database\Seeder;

class ForumCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Trồng trọt',
                'slug' => 'trong-trot',
                'description' => 'Chia sẻ kinh nghiệm về kỹ thuật trồng trọt, chăm sóc cây trồng',
            ],
            [
                'name' => 'Sâu bệnh',
                'slug' => 'sau-benh',
                'description' => 'Hỏi đáp về sâu bệnh và cách phòng trị trên cây trồng',
            ],
            [
                'name' => 'Phân bón & Thuốc',
                'slug' => 'phan-bon-thuoc',
                'description' => 'Thảo luận về phân bón, thuốc bảo vệ thực vật',
            ],
            [
                'name' => 'Nông nghiệp thông minh',
                'slug' => 'nong-nghiep-thong-minh',
                'description' => 'Ứng dụng công nghệ IoT, AI vào nông nghiệp',
            ],
            [
                'name' => 'Thị trường & Giá cả',
                'slug' => 'thi-truong-gia-ca',
                'description' => 'Cập nhật giá nông sản, thông tin thị trường',
            ],
            [
                'name' => 'Chia sẻ thành công',
                'slug' => 'chia-se-thanh-cong',
                'description' => 'Chia sẻ mô hình nông nghiệp thành công',
            ],
            [
                'name' => 'Hỏi đáp chung',
                'slug' => 'hoi-dap-chung',
                'description' => 'Các câu hỏi chung về nông nghiệp',
            ],
        ];

        foreach ($categories as $category) {
            ForumCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
