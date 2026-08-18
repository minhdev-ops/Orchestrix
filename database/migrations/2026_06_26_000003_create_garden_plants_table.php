<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('garden_plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garden_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('garden_zones')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('name');
            $table->string('species')->nullable();
            $table->string('image_url')->nullable();
            $table->string('stage')->default('seedling');
            $table->timestamp('planted_at')->useCurrent();
            $table->integer('hydration_value')->default(80);
            $table->integer('nutrient_value')->default(50);
            $table->timestamp('last_watered_at')->nullable();
            $table->timestamp('last_fertilized_at')->nullable();
            $table->string('health_status')->default('healthy');
            $table->integer('position_x')->default(0);
            $table->integer('position_y')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index('garden_id');
            $table->index('zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('garden_plants');
    }
};
