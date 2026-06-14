<?php

namespace Database\Seeders;

use App\Modules\AgriVerse\Models\SustainabilityReport;
use Illuminate\Database\Seeder;

class SustainabilityReportSeeder extends Seeder
{
    public function run(): void
    {
        SustainabilityReport::updateOrCreate(['year' => 2024], [
            'year' => 2024,
            'carbon_offset_target' => 2482,
            'carbon_offset_actual' => 2482,
            'carbon_trend' => '+12% YoY',
            'reforestation_total' => 45100,
            'reforestation_status' => 'Tăng trưởng đã xác nhận',
            'packaging_sustainable_percent' => 98.4,
            'circularity_percent' => 70,
            'supply_regions_tracked' => '24 Khu vực',
            'audit_rating' => 'A+ (SGS)',
            'ev_delivery_percent' => 82.0,
            'water_recovered_gallons' => 1200000.0,
            'pdf_url' => null,
            'title' => 'Báo cáo Thường niên 2024',
            'description' => 'Vượt lên trên vẻ đẹp thẩm mỹ của những tán lá hiếm là cam kết nghiêm ngặt về phục hồi sinh thái. Báo cáo minh bạch dựa trên dữ liệu của chúng tôi theo dõi hành trình của từng chiếc lá từ nguồn gốc đạo đức đến môi trường sống bền vững.',
            'hero_image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBWarD2zXjbfLDjZPfB6Pan5YmPT_I__sNh51QO1gnObBQEeBwRu_TxxHMgZycVXuGsXZL7NIkx0xL9TQj2qz3Q218q-gr0DMh31p5wE_cCX7zcsHb7g5JdQlvsAaBmFpmnZcVasD-_vJEn9Aq63P5R7iZIj0zFbP6AO8XjcPxSZFtaTYtWOmVxhJcL9gJmND6A-9_fMzYdhBzK1UIk9bN91JHifPWSmjGHsiknnONorbdhnmPwBe38Ro4W2O_GUrwmLVfssDtYmxY',
            'quote' => '"Chính xác trong từng cánh hoa."',
            'quote_author' => '— TS. Elena Vance, Trưởng phòng Nghiên cứu Thực vật',
            'ethical_description' => 'Theo dõi nguồn gốc mẫu vật với độ chính xác của blockchain để đảm bảo không khai thác trái phép và tiêu chuẩn lao động công bằng trên bốn châu lục.',
            'circular_description' => 'Mục tiêu của chúng tôi là 100% tuần hoàn vào năm 2026. Các chỉ số hiện tại phản ánh tỷ lệ tái sử dụng đất, chậu và vật liệu vận chuyển.',
        ]);
    }
}
