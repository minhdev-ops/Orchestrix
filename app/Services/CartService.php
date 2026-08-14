<?php

namespace App\Services;

/**
 * @deprecated CartItem model no longer exists. Module Cart uses Cart model directly.
 * Kept for reference; will be removed in next major version.
 */
class CartService
{
    public function getCart(int $userId)
    {
        return collect();
    }

    public function addItem(int $userId, int $productId, int $quantity = 1)
    {
        return null;
    }

    public function removeItem(int $userId, int $productId): void
    {
    }

    public function clearCart(int $userId): void
    {
    }

    public function getCartTotal(int $userId): float
    {
        return 0.0;
    }
}
