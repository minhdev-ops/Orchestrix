<?php

namespace Database\Seeders;

use App\Modules\AgriVerse\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizQuestionSeeder extends Seeder
{
    public function run(): void
    {
        QuizQuestion::insert([
            [
                'step' => 1,
                'question_text' => 'Điều kiện ánh sáng thế nào?',
                'choice_type' => 'grid',
                'choices' => json_encode([
                    ['label' => 'Ánh sáng Trực tiếp', 'desc' => 'Cửa sổ hướng nam sáng với 6+ giờ nắng.', 'icon' => 'wb_sunny'],
                    ['label' => 'Ánh sáng Lọc', 'desc' => 'Ánh sáng gián tiếp sáng hoặc qua rèm mỏng.', 'icon' => 'light_mode'],
                    ['label' => 'Ánh sáng Yếu', 'desc' => 'Cửa sổ hướng bắc hoặc góc râm.', 'icon' => 'filter_drama'],
                ]),
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'step' => 2,
                'question_text' => 'Độ ẩm môi trường của bạn thế nào?',
                'choice_type' => 'image',
                'choices' => json_encode([
                    ['label' => 'Độ ẩm Cao', 'sub' => 'Khí hậu nhiệt đới ưa sương mù', 'image_class' => 'choice-image-humidity'],
                    ['label' => 'Không khí Khô', 'sub' => 'Sưởi ấm gia đình hoặc văn phòng tiêu chuẩn', 'image_class' => 'choice-image-dry'],
                ]),
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'step' => 3,
                'question_text' => 'Trình độ của bạn là gì?',
                'choice_type' => 'rows',
                'choices' => json_encode([
                    ['label' => 'Người Đam mê', 'desc' => 'Cây dễ chăm, khỏe mạnh cho người mới bắt đầu.'],
                    ['label' => 'Nhà Làm vườn', 'desc' => 'Chăm sóc trung cấp cho người sẵn sàng có lịch trình.'],
                    ['label' => 'Thợ săn Mẫu vật', 'desc' => 'Cây quý hiếm, tinh tế dành cho chuyên gia tận tâm.'],
                ]),
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
