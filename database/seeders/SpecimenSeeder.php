<?php

namespace Database\Seeders;

use App\Modules\AgriVerse\Models\Specimen;
use Illuminate\Database\Seeder;

class SpecimenSeeder extends Seeder
{
    public function run(): void
    {
        Specimen::insert([
            [
                'user_id' => 1,
                'name' => 'Monstera Deliciosa',
                'code' => 'BH-09224',
                'location' => 'Living Room',
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC9WLTKhlaWjBjifoYvcYy_yUtwhxrR2W3v1Hs-gPcHkLJ6PtEoJ2RkQJ3D6Jbvly0Sgro7DxT4l7eIpi9o8YeqhPWqZon25fr_UlySezGrzLjsE-6dvQLdVVgsqd7DBt4aHszsN3L2U1UXjkmDq85U1IWQ3HhoT9S76ppTUY1uHu77vvOi3C-WFRJwsTsX0Rru43spRcoxhtHmQbcbqRdZqDdU-NleSR-QW2MpdjAnPe-6hcF_qzX2ocNxmPeQUHisDy7Cg4MzHdQ',
                'status' => 'hydrated',
                'hydration_value' => 65,
                'hydration_label' => '48h còn lại',
                'hydration_error' => false,
                'nutrient_value' => 82,
                'nutrient_label' => '12 ngày còn lại',
                'nutrient_error' => false,
                'nutrient_muted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'Ficus Lyrata',
                'code' => 'BH-11054',
                'location' => 'Studio',
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBg2GAi60_R3IKCF553FTQeHBhyF6ZK6cFvEIpr6g_FSccxpZS6sac28gKTY-aw2XucW1W-CC3Y4ezT0eFnLZ_03uwFGfmijYaoCBodfEiH78QVmx-73gO5-0THH307IAKwubK-d-cKujLKMqkraItbocGrF9kSw9br7bNg4g3RhsxixUHFeTFSmdLFPl1jNcDvg4x0vbbvxNxFcctfklTogjBeqKBWe2iKWBXCOVja0iqP9ss_oSZjBZBsbSF8BQNh2clNKZAFa4o',
                'status' => 'thirsty',
                'hydration_value' => 100,
                'hydration_label' => 'Quá hạn: 4h',
                'hydration_error' => true,
                'nutrient_value' => 15,
                'nutrient_label' => 'Sẵn sàng Bón phân',
                'nutrient_error' => false,
                'nutrient_muted' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
