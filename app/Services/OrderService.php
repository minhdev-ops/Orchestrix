<?php

namespace App\Services;

use App\Modules\AgriVerse\Events\OrderCancelled;
use App\Modules\AgriVerse\Events\OrderConfirmed;
use App\Modules\AgriVerse\Events\OrderCreated;
use App\Modules\AgriVerse\Models\Order;
use App\Modules\AgriVerse\Models\OrderStatus;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{
    protected function modelClass(): string
    {
        return Order::class;
    }

    public function placeOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create($data);
            event(new OrderCreated($order));

            return $order;
        });
    }

    public function updateStatus(Order $order, string $status, ?string $note = null): Order
    {
        return DB::transaction(function () use ($order, $status, $note) {
            $order->update(['status' => $status]);

            if ($status === 'shipping') {
                $order->update(['estimated_delivery' => now()->addDays(3)]);
            }
            if ($status === 'delivered') {
                $order->update(['delivered_at' => now()]);

                // Auto-add plant to buyer's garden if applicable
                try {
                    $gardenService = app(\App\Modules\AgriVerse\Services\GardenService::class);
                    $plant = $gardenService->addPlantFromOrder($order);
                    if ($plant) {
                        $order->buyer->notify(new \App\Modules\AgriVerse\Notifications\GardenNotification(
                            type: 'new_plant',
                            message: "🌱 {$order->product->name} đã được thêm vào khu vườn của bạn!",
                            plantId: (string) $plant->id,
                        ));
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to auto-add plant to garden: '.$e->getMessage());
                }
            }
            if ($status === 'cancelled') {
                $order->product?->increment('stock', $order->quantity);
                event(new OrderCancelled($order, $note));
            }
            if ($status === 'confirmed') {
                event(new OrderConfirmed($order));
            }

            OrderStatus::create([
                'order_id' => $order->id,
                'status' => $status,
                'note' => $note ?? "Status updated to {$status}",
                'user_id' => auth()->id(),
            ]);

            return $order->fresh();
        });
    }

    public function getOrdersForUser(int $userId, string $role = 'buyer')
    {
        $field = $role === 'seller' ? 'seller_id' : 'buyer_id';

        return Order::with(['product', 'store'])->where($field, $userId)->latest()->paginate(15);
    }
}
