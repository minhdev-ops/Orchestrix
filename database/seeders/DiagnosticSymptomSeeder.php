<?php

namespace Database\Seeders;

use App\Modules\AgriVerse\Models\DiagnosticSymptom;
use Illuminate\Database\Seeder;

class DiagnosticSymptomSeeder extends Seeder
{
    public function run(): void
    {
        DiagnosticSymptom::insert([
            ['name' => 'Vàng Lá', 'description' => 'Lá chuyển vàng, có thể do thiếu dinh dưỡng hoặc tưới quá nhiều', 'category' => 'Lá', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rụng Lá', 'description' => 'Lá rụng sớm, có thể do sốc nhiệt hoặc thay đổi môi trường', 'category' => 'Lá', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Đầu Lá Nâu', 'description' => 'Đầu lá khô nâu, thường do độ ẩm thấp hoặc tưới không đều', 'category' => 'Lá', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chậm Phát Triển', 'description' => 'Cây phát triển chậm hơn bình thường, có thể do thiếu ánh sáng hoặc dinh dưỡng', 'category' => 'Tăng trưởng', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lá Đốm', 'description' => 'Xuất hiện đốm trên lá, có thể do nấm hoặc vi khuẩn', 'category' => 'Bệnh', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mốc Trắng', 'description' => 'Lớp mốc trắng trên lá hoặc thân, do nấm powdery mildew', 'category' => 'Bệnh', 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
