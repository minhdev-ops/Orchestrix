<?php

namespace App\Modules\AgriVerse\Listeners;

use App\Modules\AgriVerse\Events\OrderCancelled;
use App\Modules\AgriVerse\Models\Product;

class RestoreInventoryOnCancel
{
    public function handle(OrderCancelled $event): void
    {
        $order = $event->order;

        if ($order->product_id && $order->quantity) {
            Product::where('id', $order->product_id)
                ->increment('stock', $order->quantity);
        }
    }
}
