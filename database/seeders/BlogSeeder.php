<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Blog\Models\BlogCategory;
use Modules\Blog\Models\BlogTag;
use Modules\Blog\Models\BlogPost;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $categoriesData = [
            ['name' => 'Laravel', 'slug' => 'laravel', 'color' => '#FF2D20', 'icon' => 'code', 'description' => 'Thực chiến với Laravel Framework'],
            ['name' => 'DevOps', 'slug' => 'devops', 'color' => '#F59E0B', 'icon' => 'settings', 'description' => 'CI/CD, Docker, Kubernetes, Cloud Infrastructure'],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'color' => '#FBBF24', 'icon' => 'javascript', 'description' => 'Vue.js, React, Node.js và hơn thế nữa'],
            ['name' => 'Database', 'slug' => 'database', 'color' => '#3B82F6', 'icon' => 'database', 'description' => 'MySQL, PostgreSQL, Redis, MongoDB'],
            ['name' => 'Tips & Tricks', 'slug' => 'tips-tricks', 'color' => '#8B5CF6', 'icon' => 'lightbulb', 'description' => 'Mẹo vặt lập trình hữu ích'],
            ['name' => 'Career', 'slug' => 'career', 'color' => '#10B981', 'icon' => 'work', 'description' => 'Phát triển sự nghiệp IT'],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[] = BlogCategory::firstOrCreate(['slug' => $catData['slug']], $catData);
        }

        // 2. Tags
        $tagNames = ['laravel', 'php', 'docker', 'nginx', 'redis', 'mysql', 'javascript', 'vue', 'api', 'migrations', 'beginner', 'advanced', 'clean-code', 'solid', 'testing', 'security'];
        $tagMap = [];
        foreach ($tagNames as $t) {
            $tag = BlogTag::firstOrCreate(['slug' => $t], ['name' => ucfirst($t)]);
            $tagMap[$t] = $tag->id;
        }

        // 3. Realistic Sample Posts
        $samples = [
            [
                'title' => 'Hướng dẫn toàn diện về Laravel Eloquent ORM',
                'cat' => 'laravel',
                'tags' => ['laravel', 'php', 'mysql', 'advanced'],
                'featured' => true,
            ],
            [
                'title' => 'Deploy Laravel với Docker Compose và Nginx',
                'cat' => 'devops',
                'tags' => ['docker', 'nginx', 'laravel'],
                'featured' => true,
            ],
            [
                'title' => '10 Tips tăng performance Laravel mà bạn chưa biết',
                'cat' => 'tips-tricks',
                'tags' => ['laravel', 'php', 'redis'],
                'featured' => false,
            ],
            [
                'title' => 'Làm chủ React Hooks trong 30 phút',
                'cat' => 'javascript',
                'tags' => ['javascript', 'vue'], // Using vue tag as placeholder
                'featured' => false,
            ],
            [
                'title' => 'Kiến trúc Microservices với Docker & Kubernetes',
                'cat' => 'devops',
                'tags' => ['docker', 'advanced'],
                'featured' => true,
            ],
            [
                'title' => 'Tối ưu hóa MySQL cho hệ thống hàng triệu bản ghi',
                'cat' => 'database',
                'tags' => ['mysql', 'database', 'advanced'],
                'featured' => true,
            ],
            [
                'title' => 'Lộ trình trở thành Senior Backend Engineer 2026',
                'cat' => 'career',
                'tags' => ['beginner', 'advanced'],
                'featured' => false,
            ],
            [
                'title' => 'Áp dụng SOLID principles trong Laravel Project',
                'cat' => 'laravel',
                'tags' => ['solid', 'clean-code', 'advanced'],
                'featured' => false,
            ],
            [
                'title' => 'Tại sao bạn nên chọn PostgreSQL thay vì MySQL?',
                'cat' => 'database',
                'tags' => ['database', 'mysql'],
                'featured' => false,
            ],
            [
                'title' => 'Bảo mật ứng dụng Web: Những lỗ hổng phổ biến',
                'cat' => 'tips-tricks',
                'tags' => ['security', 'advanced'],
                'featured' => true,
            ],
            [
                'title' => 'Cách viết Unit Test hiệu quả với Pest PHP',
                'cat' => 'laravel',
                'tags' => ['testing', 'laravel', 'php'],
                'featured' => false,
            ],
            [
                'title' => 'Redis không chỉ là Cache: Các use case thú vị',
                'cat' => 'database',
                'tags' => ['redis', 'database'],
                'featured' => false,
            ],
        ];

        foreach ($samples as $index => $s) {
            $category = BlogCategory::where('slug', $s['cat'])->first();
            
            $post = BlogPost::firstOrCreate(
                ['slug' => Str::slug($s['title'])],
                [
                    'blog_category_id' => $category->id,
                    'title' => $s['title'],
                    'excerpt' => 'Đây là nội dung tóm tắt cho bài viết "' . $s['title'] . '". Khám phá thêm các kiến thức hữu ích về ' . $category->name . '.',
                    'content_md' => "# " . $s['title'] . "\n\nNội dung chi tiết cho bài viết này đang được cập nhật. Đây là dữ liệu mẫu để kiểm tra giao diện.\n\n## Nội dung chính\n- Điểm 1\n- Điểm 2\n- Điểm 3\n\n```php\n// Sample code\npublic function test() {\n    return 'Hello World';\n}\n```",
                    'author' => 'Antigravity',
                    'is_published' => true,
                    'is_featured' => $s['featured'],
                    'views_count' => rand(100, 5000),
                    'likes_count' => rand(20, 500),
                    'published_at' => now()->subDays(rand(1, 100)),
                ]
            );

            $tagIds = array_map(fn($t) => $tagMap[$t] ?? null, $s['tags']);
            $post->tags()->sync(array_filter($tagIds));

            // Add comments for each sample post
            $comments = [
                ['name' => 'Nguyễn Văn A', 'content' => 'Bài viết rất hữu ích, cảm ơn tác giả!'],
                ['name' => 'Trần Thị B', 'content' => 'Mình đang gặp vấn đề này, bài viết giải quyết đúng chỗ ngứa.'],
                ['name' => 'Lê Văn C', 'content' => 'Có thể viết thêm về phần nâng cao không bạn?'],
            ];
            foreach ($comments as $c) {
                \Modules\Blog\Models\BlogComment::create([
                    'blog_post_id' => $post->id,
                    'name' => $c['name'],
                    'email' => Str::slug($c['name']) . '@example.com',
                    'content' => $c['content'],
                    'is_approved' => true,
                ]);
            }
        }

        // Add some random posts to fill up the space
        for ($i = 1; $i <= 10; $i++) {
            $cat = $categories[array_rand($categories)];
            $title = "Bài viết ngẫu nhiên số " . $i . " về " . $cat->name;
            $post = BlogPost::create([
                'blog_category_id' => $cat->id,
                'title' => $title,
                'slug' => Str::slug($title) . '-' . uniqid(),
                'excerpt' => 'Một bài viết ngẫu nhiên được tạo ra để kiểm tra tính năng phân trang và hiển thị grid.',
                'content_md' => "# " . $title . "\n\nNội dung ngẫu nhiên...",
                'author' => 'System',
                'is_published' => true,
                'is_featured' => false,
                'views_count' => rand(10, 500),
                'likes_count' => rand(0, 50),
                'published_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Update category post counts
        foreach (BlogCategory::all() as $cat) {
            $cat->update(['posts_count' => BlogPost::where('blog_category_id', $cat->id)->where('is_published', true)->count()]);
        }

        $this->command->info('✅ Blog seeded: ' . BlogCategory::count() . ' categories, ' . BlogTag::count() . ' tags, ' . BlogPost::count() . ' posts.');
    }
}

