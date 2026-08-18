<?php

namespace Database\Factories;

use App\Models\User;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $product = Product::factory()->create();
        $buyer = User::factory()->create(['role' => 'buyer']);

        return [
            'product_id' => $product->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $product->user_id,
            'quantity' => 1,
            'unit_price' => $product->price,
            'total_price' => $product->price,
            'total_amount' => $product->price,
            'status' => 'pending',
            'shipping_address' => fake()->address(),
        ];
    }
}
