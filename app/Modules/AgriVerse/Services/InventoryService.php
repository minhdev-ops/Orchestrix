<?php

namespace App\Modules\AgriVerse\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\Order;

class InventoryService
{
    /**
     * Get current stock for a product
     */
    public function getStock(int $productId): int
    {
        return Product::where('id', $productId)->value('stock') ?? 0;
    }

    /**
     * Check if product is in stock
     */
    public function isInStock(int $productId, int $quantity = 1): bool
    {
        return $this->getStock($productId) >= $quantity;
    }

    /**
     * Decrease stock when order is placed
     */
    public function decreaseStock(Product $product, int $quantity): bool
    {
        if (!$this->isInStock($product->id, $quantity)) {
            return false;
        }

        $product->decrement('stock', $quantity);

        // Log inventory change
        $this->logInventoryChange($product->id, 'decrease', $quantity, $product->stock);

        // Check for low stock alert
        $this->checkLowStock($product);

        return true;
    }

    /**
     * Increase stock (e.g., when order is cancelled/refunded)
     */
    public function increaseStock(Product $product, int $quantity): bool
    {
        $product->increment('stock', $quantity);

        // Log inventory change
        $this->logInventoryChange($product->id, 'increase', $quantity, $product->stock);

        return true;
    }

    /**
     * Set stock directly
     */
    public function setStock(Product $product, int $quantity): bool
    {
        $oldStock = $product->stock;
        $product->update(['stock' => $quantity]);

        $this->logInventoryChange($product->id, 'set', $quantity, $quantity, $oldStock);

        return true;
    }

    /**
     * Reserve stock for an order (soft reserve)
     */
    public function reserveStock(int $productId, int $quantity, int $orderId): bool
    {
        $product = Product::find($productId);
        if (!$product || !$this->isInStock($productId, $quantity)) {
            return false;
        }

        $product->decrement('stock', $quantity);

        // Store reservation
        DB::table('inventory_reservations')->insert([
            'product_id' => $productId,
            'order_id' => $orderId,
            'quantity' => $quantity,
            'reserved_at' => now(),
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
        ]);

        $this->logInventoryChange($productId, 'reserve', $quantity, $product->stock);
        $this->checkLowStock($product);

        return true;
    }

    /**
     * Release reserved stock
     */
    public function releaseReservation(int $orderId): bool
    {
        $reservations = DB::table('inventory_reservations')
            ->where('order_id', $orderId)
            ->whereNull('released_at')
            ->get();

        foreach ($reservations as $reservation) {
            $product = Product::find($reservation->product_id);
            if ($product) {
                $product->increment('stock', $reservation->quantity);
                $this->logInventoryChange($reservation->product_id, 'release', $reservation->quantity, $product->stock);
            }

            DB::table('inventory_reservations')
                ->where('id', $reservation->id)
                ->update(['released_at' => now()]);
        }

        return true;
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(int $threshold = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('stock', '<=', $threshold)
            ->where('stock', '>', 0)
            ->where('is_active', true)
            ->with('store')
            ->orderBy('stock')
            ->get();
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('stock', '<=', 0)
            ->where('is_active', true)
            ->with('store')
            ->get();
    }

    /**
     * Get inventory summary
     */
    public function getSummary(): array
    {
        $products = Product::where('is_active', true);

        return [
            'total_products' => (clone $products)->count(),
            'in_stock' => (clone $products)->where('stock', '>', 0)->count(),
            'out_of_stock' => (clone $products)->where('stock', '<=', 0)->count(),
            'low_stock' => (clone $products)->where('stock', '<=', 10)->where('stock', '>', 0)->count(),
            'total_stock_value' => (clone $products)->selectRaw('SUM(stock * price) as value')->value('value') ?? 0,
        ];
    }

    /**
     * Get inventory history
     */
    public function getHistory(int $productId, int $limit = 50): array
    {
        return DB::table('inventory_logs')
            ->where('product_id', $productId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Log inventory change
     */
    protected function logInventoryChange(
        int $productId,
        string $type,
        int $quantity,
        int $newStock,
        ?int $oldStock = null
    ): void {
        DB::table('inventory_logs')->insert([
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $quantity,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);
    }

    /**
     * Check and send low stock alert
     */
    protected function checkLowStock(Product $product, int $threshold = 10): void
    {
        if ($product->stock <= $threshold && $product->stock > 0) {
            Log::warning("Low stock alert: Product #{$product->id} ({$product->name}) has only {$product->stock} units left");

            // Notify store owner
            if ($product->store && $product->store->owner) {
                $product->store->owner->notify(new \App\Notifications\LowStockNotification($product));
            }
        }
    }

    /**
     * Bulk update stock
     */
    public function bulkUpdateStock(array $updates): array
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($updates as $update) {
            try {
                $product = Product::find($update['product_id']);
                if (!$product) {
                    $results['failed']++;
                    $results['errors'][] = "Product #{$update['product_id']} not found";
                    continue;
                }

                $this->setStock($product, $update['stock']);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = $e->getMessage();
            }
        }

        return $results;
    }
}
