<?php

namespace App\Modules\AgriVerse\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use App\Modules\AgriVerse\Models\Order;

class OrderCreated
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public Order $order) {}
}
