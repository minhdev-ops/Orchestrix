<?php

namespace Database\Seeders;

use App\Modules\AgriVerse\Models\Manufacturer;
use App\Modules\AgriVerse\Models\ProductType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AgriVerseReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Cây bonsai cổ thụ', 'Cây cảnh mini để bàn', 'Sen đá các loại',
            'Xương rồng cảnh', 'Cây thủy sinh thân rễ', 'Cây thủy sinh thân cột',
            'Bonsai ăn quả', 'Cây leo giàn cảnh', 'Cây nội thất phong thủy',
            'Cây dây leo văn phòng', 'Cây không khí (Tillandsia)',
        ];

        foreach ($types as $name) {
            ProductType::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name), 'is_active' => true]
            );
        }

        $manufacturers = [
            'Vườn bonsai làng nghề Phú Thọ', 'Nghệ nhân bonsai Nguyễn Văn Tài',
            'Vườn ươm cây cảnh Nhật Linh', 'Bonsai Garden Hà Nội',
            'Cây cảnh nghệ thuật Sài Gòn', 'Vườn sen đá Đà Lạt',
            'Nông trại xương rồng Ninh Thuận', 'Vườn thủy sinh Phong Lan',
            'Bonsai nghệ thuật Huế', 'Cây cảnh mini Đan Phượng',
            'Vườn ươm Thanh Hà', 'Vườn bonsai Tam Kỳ',
            'Nghệ nhân bonsai Phạm Văn Hùng', 'Vườn cây nội thất Xanh',
            'Cây cảnh phong thủy Linh Phương', 'Vườn ươm Hải Đăng',
            'Bonsai cổ thụ Đà Nẵng', 'Sen đá nghệ thuật Miền Trung',
            'Vườn thủy sinh Cần Thơ', 'Xương rồng quý hiếm Bình Thuận',
            'Bonsai ăn quả Long An', 'Vườn cây đề bàn Sài Gòn',
            'Vườn ươm Hoàng Gia', 'Nghệ nhân bonsai Trần Minh Đức',
            'Vườn cây cảnh Quảng Ngãi', 'Vườn tilandsia miền Tây',
            'Cây cảnh văn phòng Ecolife', 'Bonsai Garden Đà Lạt',
            'Vườn ươm Phú Sinh', 'Nghệ nhân bonsai Nguyễn Thanh Sơn',
            'Vườn bonsai mini Việt Hàn', 'Cây thủy sinh nhập khẩu',
            'Vườn sen đá nghìn viên', 'Xương rồng cảnh sa mạc',
            'Bonsai siêu nhỏ Hà Nội', 'Vườn cây cảnh Mê Linh',
            'Chợ cây cảnh online BonsaiPro', 'Vườn ươm Đông Phương',
            'Nghệ nhân bonsai Huỳnh Văn Pháp', 'Cây nội thất bóng mát',
        ];

        foreach ($manufacturers as $name) {
            Manufacturer::firstOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
