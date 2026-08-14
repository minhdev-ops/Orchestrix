<?php

namespace App\Modules\AgriVerse\Events;

use App\Modules\AgriVerse\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class OrderCancelled
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public Order $order, public ?string $reason = null) {}
}
