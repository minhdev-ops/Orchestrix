<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\AgriVerse\Database\Seeders\ForumCategorySeeder;
use App\Modules\AgriVerse\Models\Category;
use App\Modules\AgriVerse\Models\Store;
use App\Modules\AgriVerse\Models\SubscriptionPlan;
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

        Store::firstOrCreate(
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

        // --- Reference Seeders ---
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
                ['name' => 'Bonsai cổ thụ', 'slug' => 'bonsai-co-thu', 'icon' => 'forest', 'sort_order' => 1, 'is_active' => true],
                ['name' => 'Cây cảnh mini', 'slug' => 'cay-canh-mini', 'icon' => 'potted_plant', 'sort_order' => 2, 'is_active' => true],
                ['name' => 'Sen đá & Xương rồng', 'slug' => 'sen-da-xuong-rong', 'icon' => 'local_flora', 'sort_order' => 3, 'is_active' => true],
                ['name' => 'Cây thủy sinh', 'slug' => 'cay-thuy-sinh', 'icon' => 'pond', 'sort_order' => 4, 'is_active' => true],
                ['name' => 'Cây ăn quả bonsai', 'slug' => 'cay-an-qua-bonsai', 'icon' => 'yard', 'sort_order' => 5, 'is_active' => true],
                ['name' => 'Chậu & Kệ cảnh', 'slug' => 'chau-ke-canh', 'icon' => 'pottery', 'sort_order' => 6, 'is_active' => true],
                ['name' => 'Đất & Phân bón', 'slug' => 'dat-phan-bon', 'icon' => 'compost', 'sort_order' => 7, 'is_active' => true],
                ['name' => 'Phụ kiện & Dụng cụ', 'slug' => 'phu-kien-dung-cu', 'icon' => 'handyman', 'sort_order' => 8, 'is_active' => true],
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
