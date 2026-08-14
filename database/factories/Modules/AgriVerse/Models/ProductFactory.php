<?php

namespace Database\Factories\Modules\AgriVerse\Models;

use App\Models\User;
use App\Modules\AgriVerse\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'price' => fake()->numberBetween(10000, 5000000),
            'stock' => fake()->numberBetween(1, 100),
            'status' => 'published',
            'description' => fake()->paragraph(),
        ];
    }
}
