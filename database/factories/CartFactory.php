<?php

namespace Database\Factories;

use App\Models\User;
use App\Modules\AgriVerse\Models\Cart;
use App\Modules\AgriVerse\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }
}
