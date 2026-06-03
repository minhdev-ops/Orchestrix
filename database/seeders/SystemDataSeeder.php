<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemDataSeeder extends Seeder
{
    public function run(): void
    {
        // Portfolio Data
        DB::table('projects')->updateOrInsert(
            ['slug' => 'cyber-safe'],
            [
                'title' => 'Dự án Cyber-Safe',
                'description' => 'Hệ thống bảo mật tiên tiến.',
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
