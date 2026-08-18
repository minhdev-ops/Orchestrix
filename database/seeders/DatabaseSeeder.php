<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\AgriVerse\Database\Seeders\ForumCategorySeeder;
use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\SubscriptionPlan;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Category;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            ForumCategorySeeder::class,
        ]);

        // --- Core Users ---
        $admin = User::firstOrCreate(
            ['email' => 'admin@orchestrix.com'],
            ['name' => 'Admin', 'password' => bcrypt('12345678'), 'role' => User::ROLE_ADMIN]
        );
        if (! $admin->hasRole(User::ROLE_ADMIN)) {
            $admin->assignRole(User::ROLE_ADMIN);
            $admin->assignRole(Role::where('name', User::ROLE_ADMIN)->where('guard_name', 'api')->first());
        }

        // Seller user + store for product seeding
        $seller = User::firstOrCreate(
            ['email' => 'seller@orchestrix.com'],
            ['name' => 'Seller', 'password' => bcrypt('12345678'), 'role' => User::ROLE_SELLER, 'is_active' => 1]
        );
        if (! $seller->hasRole(User::ROLE_SELLER)) {
            $seller->assignRole(User::ROLE_SELLER);
            $seller->assignRole(Role::where('name', User::ROLE_SELLER)->where('guard_name', 'api')->first());
        }

        $employee = User::firstOrCreate(
            ['email' => 'employee@orchestrix.com'],
            ['name' => 'Employee', 'password' => bcrypt('12345678'), 'role' => User::ROLE_EMPLOYEE]
        );
        if (!$employee->hasRole(User::ROLE_EMPLOYEE)) {
            $employee->assignRole(User::ROLE_EMPLOYEE);
            $employee->assignRole(\Spatie\Permission\Models\Role::where('name', User::ROLE_EMPLOYEE)->where('guard_name', 'api')->first());
        }

        $buyer = User::firstOrCreate(
            ['email' => 'buyer@orchestrix.com'],
            ['name' => 'Buyer', 'password' => bcrypt('12345678'), 'role' => User::ROLE_BUYER]
        );
        if (!$buyer->hasRole(User::ROLE_BUYER)) {
            $buyer->assignRole(User::ROLE_BUYER);
            $buyer->assignRole(\Spatie\Permission\Models\Role::where('name', User::ROLE_BUYER)->where('guard_name', 'api')->first());
        }

        $store = Store::firstOrCreate(
            ['owner_id' => $seller->id],
            ['name' => 'Vườn Bonsai Empire', 'description' => 'Cửa hàng cây cảnh chất lượng cao - nguồn dữ liệu từ BonsaiEmpire.vn và các web cây cảnh Việt Nam.', 'status' => 'active']
        );

        // --- Subscription Plans ---
        $plans = [
            ['name' => 'Cơ bản', 'limit_3d_models' => 10, 'price_per_month' => 29.99, 'features' => ['products_limit' => 10, 'assets_limit' => 50, 'storage_gb' => 5]],
            ['name' => 'Chuyên nghiệp', 'limit_3d_models' => 100, 'price_per_month' => 99.99, 'features' => ['products_limit' => 100, 'assets_limit' => 500, 'storage_gb' => 50]],
            ['name' => 'Cao cấp', 'limit_3d_models' => 500, 'price_per_month' => 299.99, 'features' => ['products_limit' => -1, 'assets_limit' => -1, 'storage_gb' => 500]],
        ];
        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(['name' => $plan['name']], $plan);
        }

        $products = [
            [
                'name' => 'Bonsai San Jose Juniper',
                'description' => 'Bonsai San Jose Juniper dáng trực, tuổi đời 15 năm, đã qua tạo tác bởi nghệ nhân làng nghề.',
                'price' => 2500000, 'stock' => 5, 'category' => 'bonsai-co-thu',
                'image' => 'https://placehold.co/400x400/2d5016/ffffff?text=Bonsai+San+Jose+Juniper',
            ],
            [
                'name' => 'Bonsai đa lộc dáng huyền',
                'description' => 'Bonsai đa lộc dáng huyền cổ thụ, tuổi đời trên 20 năm, thế uốn lượn mềm mại.',
                'price' => 4500000, 'stock' => 2, 'category' => 'bonsai-co-thu',
                'image' => 'https://placehold.co/400x400/2d5016/ffffff?text=Bonsai+Da+Loc',
            ],
            [
                'name' => 'Sen đá Echeveria đỏ viền',
                'description' => 'Sen đá Echeveria nhập khẩu, lá mọng nước viền đỏ, thích hợp làm cây cảnh mini để bàn.',
                'price' => 85000, 'stock' => 50, 'category' => 'sen-da-xuong-rong',
                'image' => 'https://placehold.co/400x400/a5c882/1a1a1a?text=Sen+Da+Echeveria',
            ],
            [
                'name' => 'Xương rồng sa mạc vàng',
                'description' => 'Xương rồng cảnh sa mạc cao 25cm, dễ chăm sóc, ra hoa vàng rực vào mùa hè.',
                'price' => 120000, 'stock' => 30, 'category' => 'sen-da-xuong-rong',
                'image' => 'https://placehold.co/400x400/a5c882/1a1a1a?text=Xuong+Rong+Sa+Mac',
            ],
            [
                'name' => 'Cây kim ngân (Pachira Aquatica)',
                'description' => 'Cây kim ngân bonsai mini, thân bện 3 tết, phong thủy mang lại tài lộc.',
                'price' => 350000, 'stock' => 15, 'category' => 'cay-canh-mini',
                'image' => 'https://placehold.co/400x400/2d5016/ffffff?text=Kim+Ngan+Bonsai',
            ],
            [
                'name' => 'Cây lưỡi hổ mini để bàn',
                'description' => 'Cây lưỡi hổ cảnh mini, cao 15cm, chịu bóng tốt, lọc không khí hiệu quả.',
                'price' => 65000, 'stock' => 40, 'category' => 'cay-canh-mini',
                'image' => 'https://placehold.co/400x400/a5c882/1a1a1a?text=Luoi+Ho+Mini',
            ],
            [
                'name' => 'Cây thủy sinh Anubias Nana',
                'description' => 'Cây thủy sinh Anubias Nana thân rễ, dễ trồng, phù hợp hồ thủy sinh mini.',
                'price' => 45000, 'stock' => 60, 'category' => 'cay-thuy-sinh',
                'image' => 'https://placehold.co/400x400/4a90d9/ffffff?text=Anubias+Nana',
            ],
            [
                'name' => 'Rêu Java (Vesicularia Dubyana)',
                'description' => 'Rêu Java thủy sinh, dễ dàng tạo thảm xanh cho hồ thủy sinh mọi kích thước.',
                'price' => 35000, 'stock' => 80, 'category' => 'cay-thuy-sinh',
                'image' => 'https://placehold.co/400x400/4a90d9/ffffff?text=Reu+Java',
            ],
            [
                'name' => 'Bonsai ổi cảnh trái vàng',
                'description' => 'Bonsai ổi cảnh ra trái vàng óng, tuổi đời 8 năm, thích hợp sân vườn tiểu cảnh.',
                'price' => 3200000, 'stock' => 3, 'category' => 'cay-an-qua-bonsai',
                'image' => 'https://placehold.co/400x400/2d5016/ffffff?text=Bonsai+Oi+Canh',
            ],
            [
                'name' => 'Bonsai khế cảnh trái chùm',
                'description' => 'Bonsai khế cảnh dáng cổ thụ, sai trái chùm quanh năm, mang ý nghĩa may mắn.',
                'price' => 2800000, 'stock' => 4, 'category' => 'cay-an-qua-bonsai',
                'image' => 'https://placehold.co/400x400/2d5016/ffffff?text=Bonsai+Khe+Canh',
            ],
            [
                'name' => 'Chậu gốm Bát Tràng men xanh',
                'description' => 'Chậu gốm Bát Tràng men xanh lam, đường kính 30cm, cao 25cm, họa tiết hoa sen.',
                'price' => 450000, 'stock' => 20, 'category' => 'chau-ke-canh',
                'image' => 'https://placehold.co/400x400/d9a05b/ffffff?text=Chau+Go+Bat+Trang',
            ],
            [
                'name' => 'Kệ gỗ bonsai 3 tầng',
                'description' => 'Kệ gỗ bonsai 3 tầng bằng gỗ sồi tự nhiên, kích thước 80x35x90cm.',
                'price' => 890000, 'stock' => 10, 'category' => 'chau-ke-canh',
                'image' => 'https://placehold.co/400x400/8B6914/ffffff?text=Ke+Go+Bonsai',
            ],
            [
                'name' => 'Đất trồng bonsai Akadama Nhật',
                'description' => 'Đất Akadama nhập khẩu Nhật Bản, đóng túi 5kg, chuyên dùng cho bonsai.',
                'price' => 120000, 'stock' => 100, 'category' => 'dat-phan-bon',
                'image' => 'https://placehold.co/400x400/8B4513/ffffff?text=Akadama+Nhat',
            ],
            [
                'name' => 'Phân bón NPK tan chậm cho bonsai',
                'description' => 'Phân bón viên nén tan chậm dành riêng cho bonsai, giúp lá xanh và rễ khỏe.',
                'price' => 65000, 'stock' => 200, 'category' => 'dat-phan-bon',
                'image' => 'https://placehold.co/400x400/8B4513/ffffff?text=Phan+Bon+NPK',
            ],
            [
                'name' => 'Bộ kéo cắt tỉa bonsai chuyên nghiệp',
                'description' => 'Bộ 3 kéo cắt tỉa bonsai Nhật Bản: kéo cắt cành, kéo tỉa lá, kềm cắt dây.',
                'price' => 390000, 'stock' => 25, 'category' => 'phu-kien-dung-cu',
                'image' => 'https://placehold.co/400x400/666666/ffffff?text=Keo+Cat+Tia',
            ],
            [
                'name' => 'Dây đồng uốn cành bonsai 2mm',
                'description' => 'Dây đồng uốn cành bonsai 2mm x 5m, dẻo dai, không gỉ, dễ tạo dáng.',
                'price' => 45000, 'stock' => 150, 'category' => 'phu-kien-dung-cu',
                'image' => 'https://placehold.co/400x400/666666/ffffff?text=Day+Dong+Uon+Canh',
            ],
        ];

        foreach ($products as $data) {
            Product::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, [
                    'user_id' => $seller->id,
                    'store_id' => $store->id,
                    'status' => 'published',
                ])
            );
        }

        $this->call([
            AgriVerseReferenceSeeder::class,
            SupportFaqSeeder::class,
            QuizQuestionSeeder::class,
            DiagnosticSymptomSeeder::class,
            SustainabilityReportSeeder::class,
        ]);

        if (\App\Modules\AgriVerse\Models\JournalArticle::count() === 0) {
            $this->call(JournalArticleSeeder::class);
        }
        if (\App\Modules\AgriVerse\Models\Specimen::count() === 0) {
            $this->call(SpecimenSeeder::class);
        }

        // --- Categories ---
        if (Category::count() === 0) {
            $categories = [
                ['name' => 'Bonsai cổ thụ', 'slug' => 'bonsai-co-thu', 'icon' => 'forest', 'sort_order' => 1],
                ['name' => 'Cây cảnh mini', 'slug' => 'cay-canh-mini', 'icon' => 'potted_plant', 'sort_order' => 2],
                ['name' => 'Sen đá & Xương rồng', 'slug' => 'sen-da-xuong-rong', 'icon' => 'local_flora', 'sort_order' => 3],
                ['name' => 'Cây thủy sinh', 'slug' => 'cay-thuy-sinh', 'icon' => 'pond', 'sort_order' => 4],
                ['name' => 'Cây ăn quả bonsai', 'slug' => 'cay-an-qua-bonsai', 'icon' => 'yard', 'sort_order' => 5],
                ['name' => 'Chậu & Kệ cảnh', 'slug' => 'chau-ke-canh', 'icon' => 'pottery', 'sort_order' => 6],
                ['name' => 'Đất & Phân bón', 'slug' => 'dat-phan-bon', 'icon' => 'compost', 'sort_order' => 7],
                ['name' => 'Phụ kiện & Dụng cụ', 'slug' => 'phu-kien-dung-cu', 'icon' => 'handyman', 'sort_order' => 8],
            ];
            foreach ($categories as $cat) {
                Category::create($cat);
            }
        }

        Category::whereNull('is_active')->orWhere('is_active', false)->update(['is_active' => true]);

        // --- Import dữ liệu thật từ BonsaiEmpire ---
        $this->command->info('');
        $this->command->info('🌿 Import dữ liệu từ BonsaiEmpire.vn...');

        // Copy CSV files to seeder data directory
        $this->prepareCsvFiles();

        $this->call([
            TreeSpeciesSeeder::class,
            CareGuideSeeder::class,
            BonsaiStyleSeeder::class,
            VietnamesePlantSeeder::class,
            ProductSeeder::class,
        ]);

        $this->command->info('✅ Hoàn tất import dữ liệu BonsaiEmpire!');
    }

    /**
     * Copy CSV files from docs/ to database/seeders/data/
     */
    private function prepareCsvFiles(): void
    {
        $dataDir = database_path('seeders/data');
        if (! is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }

        $files = [
            'docs/bonsaiempire_tree_species.csv' => 'tree_species.csv',
            'docs/bonsaiempire_care_guides.csv' => 'care_guides.csv',
            'docs/bonsaiempire_blog_styles.csv' => 'blog_styles.csv',
            'docs/vietnamese_plants_comprehensive.csv' => 'vietnamese_plants.csv',
        ];

        foreach ($files as $source => $dest) {
            $sourcePath = base_path($source);
            $destPath = $dataDir.'/'.$dest;
            if (file_exists($sourcePath) && ! file_exists($destPath)) {
                copy($sourcePath, $destPath);
                $this->command->info("  📄 Copied $source → database/seeders/data/$dest");
            }
        }
    }
}
