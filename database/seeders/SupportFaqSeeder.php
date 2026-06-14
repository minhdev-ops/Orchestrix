<?php

namespace Database\Seeders;

use App\Modules\AgriVerse\Models\SupportFaq;
use Illuminate\Database\Seeder;

class SupportFaqSeeder extends Seeder
{
    public function run(): void
    {
        SupportFaq::insert([
            ['question' => 'Làm thế nào để theo dõi đơn hàng của tôi?', 'answer' => 'Bạn có thể theo dõi đơn hàng trong mục "Theo dõi vận chuyển" trên trang cá nhân, nhập mã đơn hàng để xem trạng thái mới nhất.', 'category' => 'Vận chuyển', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Chính sách đổi trả như thế nào?', 'answer' => 'Chúng tôi có chính sách bảo hành sức khỏe 30 ngày cho tất cả mẫu vật. Nếu cây không đạt tiêu chuẩn, vui lòng liên hệ để được hỗ trợ.', 'category' => 'Đơn hàng', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Tôi có thể đặt lịch tư vấn chuyên gia không?', 'answer' => 'Có, bạn có thể đặt lịch tư vấn 1-1 với các nhà thực vật học của chúng tôi qua mục "Tư Vấn Chuyên Gia".', 'category' => 'Dịch vụ', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Cây của tôi có dấu hiệu bị sâu bệnh, tôi nên làm gì?', 'answer' => 'Hãy sử dụng công cụ Chẩn đoán AI để chụp ảnh và nhận phân tích. Hoặc liên hệ trực tiếp với đội ngũ chăm sóc cây trồng của chúng tôi.', 'category' => 'Chăm sóc', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Thời gian giao hàng trung bình là bao lâu?', 'answer' => 'Giao hàng nội thành trong 2-3 ngày làm việc, ngoại thành 5-7 ngày làm việc. Chúng tôi sử dụng dịch vụ GHN để đảm bảo chất lượng.', 'category' => 'Vận chuyển', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Làm thế nào để trở thành đối tác bán buôn?', 'answer' => 'Vui lòng điền form liên hệ với tiêu đề "Đối Tác Bán Buôn" và đội ngũ kinh doanh sẽ liên hệ với bạn trong vòng 48h.', 'category' => 'Đối tác', 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
