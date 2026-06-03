<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Blog Data
        $cat = DB::table('blog_categories')->where('slug', 'cong-nghe')->first();
        if (!$cat) {
            $catId = DB::table('blog_categories')->insertGetId([
                'name' => 'Công nghệ',
                'slug' => 'cong-nghe',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $catId = $cat->id;
        }

        DB::table('blog_posts')->updateOrInsert(
            ['slug' => 'chao-mung-den-voi-orchestrix'],
            [
                'blog_category_id' => $catId,
                'title' => 'Chào mừng đến với Orchestrix',
                'excerpt' => 'Đây là bài viết đầu tiên trên hệ thống.',
                'content_html' => '<p>Nội dung chào mừng bằng HTML cực đẹp.</p>',
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );


    }
}
