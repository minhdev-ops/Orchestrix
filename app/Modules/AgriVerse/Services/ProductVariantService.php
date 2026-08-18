<?php

namespace App\Modules\AgriVerse\Services;

use App\Modules\AgriVerse\Models\Product;
use App\Modules\AgriVerse\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;

class ProductVariantService
{
    /**
     * Get variants for a product
     */
    public function getVariants(int $productId): Collection
    {
        return ProductVariant::where('product_id', $productId)
            ->active()
            ->orderBy('price')
            ->get();
    }

    /**
     * Get variant by attributes
     */
    public function getVariantByAttributes(int $productId, array $attributes): ?ProductVariant
    {
        return ProductVariant::where('product_id', $productId)
            ->active()
            ->where('attributes', json_encode($attributes))
            ->first();
    }

    /**
     * Create a variant
     */
    public function create(Product $product, array $data): ProductVariant
    {
        $data['sku'] = $data['sku'] ?? $this->generateSku($product, $data);

        return ProductVariant::create(array_merge($data, [
            'product_id' => $product->id,
        ]));
    }

    /**
     * Update a variant
     */
    public function update(ProductVariant $variant, array $data): ProductVariant
    {
        $variant->update($data);

        return $variant->fresh();
    }

    /**
     * Delete a variant
     */
    public function delete(ProductVariant $variant): bool
    {
        return $variant->delete();
    }

    /**
     * Get available stock for a variant
     */
    public function getAvailableStock(int $variantId, int $quantity = 1): bool
    {
        $variant = ProductVariant::find($variantId);

        return $variant && $variant->stock >= $quantity;
    }

    /**
     * Decrease variant stock
     */
    public function decreaseStock(ProductVariant $variant, int $quantity): bool
    {
        if ($variant->stock < $quantity) {
            return false;
        }
        $variant->decrement('stock', $quantity);

        return true;
    }

    /**
     * Increase variant stock
     */
    public function increaseStock(ProductVariant $variant, int $quantity): void
    {
        $variant->increment('stock', $quantity);
    }

    /**
     * Get price range for a product
     */
    public function getPriceRange(int $productId): array
    {
        $variants = ProductVariant::where('product_id', $productId)
            ->active()
            ->get();

        if ($variants->isEmpty()) {
            $product = Product::find($productId);

            return [
                'min' => $product?->price ?? 0,
                'max' => $product?->price ?? 0,
            ];
        }

        return [
            'min' => $variants->min('price'),
            'max' => $variants->max('price'),
        ];
    }

    /**
     * Get all attributes for a product
     */
    public function getProductAttributes(int $productId): array
    {
        $variants = ProductVariant::where('product_id', $productId)
            ->active()
            ->whereNotNull('attributes')
            ->get();

        $attributes = [];
        foreach ($variants as $variant) {
            foreach ($variant->attributes as $key => $value) {
                if (! isset($attributes[$key])) {
                    $attributes[$key] = [];
                }
                if (! in_array($value, $attributes[$key])) {
                    $attributes[$key][] = $value;
                }
            }
        }

        return $attributes;
    }

    /**
     * Generate SKU
     */
    protected function generateSku(Product $product, array $data): string
    {
        $prefix = strtoupper(substr($product->name, 0, 3));
        $attrHash = md5(json_encode($data['attributes'] ?? []));

        return "{$prefix}-{$product->id}-".substr($attrHash, 0, 6);
    }

    /**
     * Bulk create variants from attributes
     */
    public function bulkCreateFromAttributes(Product $product, array $attributeCombinations): array
    {
        $variants = [];

        foreach ($attributeCombinations as $combination) {
            $variants[] = $this->create($product, [
                'name' => collect($combination)->implode(' - '),
                'price' => $product->price,
                'stock' => 0,
                'attributes' => $combination,
            ]);
        }

        return $variants;
    }
}
