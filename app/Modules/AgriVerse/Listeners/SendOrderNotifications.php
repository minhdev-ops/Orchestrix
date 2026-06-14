<?php

namespace App\Modules\AgriVerse\Listeners;

use App\Modules\AgriVerse\Events\OrderCreated;
use App\Modules\AgriVerse\Notifications\OrderNotification;

class SendOrderNotifications
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $order->loadMissing(['buyer', 'seller']);

        $buyer = $order->buyer;
        $seller = $order->seller;

        if ($seller) {
            $seller->notify(new OrderNotification(
                type: 'new_order',
                message: "Bạn có đơn hàng mới từ {$buyer?->name}",
                orderId: (string) $order->id,
            ));
        }

        if ($buyer) {
            $buyer->notify(new OrderNotification(
                type: 'order_placed',
                message: "Đơn hàng #{$order->id} đã được đặt thành công",
                orderId: (string) $order->id,
            ));
        }
    }
}
