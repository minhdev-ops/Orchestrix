<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('blog_comments');
        Schema::dropIfExists('blogs');
    }

    public function down(): void
    {
    }
};
