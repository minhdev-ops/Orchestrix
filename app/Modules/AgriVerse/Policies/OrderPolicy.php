<?php

namespace App\Modules\AgriVerse\Policies;

use App\Models\User;
use App\Modules\AgriVerse\Models\Order;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'employee']);
    }

    public function view(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['admin', 'employee'])
            || $order->buyer_id === $user->id
            || $order->seller_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('buyer');
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->hasAnyRole(['admin', 'employee'])) {
            return true;
        }

        if ($order->buyer_id === $user->id) {
            return in_array($order->status, ['pending', 'confirmed']);
        }

        if ($order->seller_id === $user->id) {
            return in_array($order->status, ['pending', 'confirmed', 'delivered']);
        }

        return false;
    }

    public function cancel(User $user, Order $order): bool
    {
        if ($order->buyer_id === $user->id) {
            return in_array($order->status, ['pending', 'confirmed']);
        }

        return $user->hasAnyRole(['admin', 'employee']);
    }

    public function confirm(User $user, Order $order): bool
    {
        return $order->seller_id === $user->id && $order->status === 'pending'
            || $user->hasAnyRole(['admin', 'employee']);
    }

    public function deliver(User $user, Order $order): bool
    {
        return $order->seller_id === $user->id && $order->status === 'confirmed'
            || $user->hasAnyRole(['admin', 'employee']);
    }

    public function complete(User $user, Order $order): bool
    {
        return $order->buyer_id === $user->id && $order->status === 'delivered'
            || $user->hasAnyRole(['admin', 'employee']);
    }
}
