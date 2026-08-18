<?php

namespace App\Services;

use App\Modules\AgriVerse\Models\DigitalPassportLog;
use App\Modules\AgriVerse\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService extends BaseService
{
    protected function modelClass(): string
    {
        return Product::class;
    }

    public function approve(Product $product, ?string $note = null): Product
    {
        $userId = Auth::id();

        return DB::transaction(function () use ($product, $userId) {
            $product->update([
                'status' => 'published',
                'reject_reason' => null,
            ]);
            DigitalPassportLog::create([
                'product_id' => $product->id,
                'action' => 'product_approved',
                'data' => ['approved_by' => $userId, 'approved_at' => now()->toDateTimeString()],
                'performed_by' => $userId,
            ]);

            return $product->fresh();
        });
    }

    public function reject(Product $product, string $reason): Product
    {
        $userId = Auth::id();

        return DB::transaction(function () use ($product, $reason, $userId) {
            $product->update(['status' => 'rejected', 'reject_reason' => $reason]);
            DigitalPassportLog::create([
                'product_id' => $product->id,
                'action' => 'product_rejected',
                'data' => ['rejected_by' => $userId, 'reason' => $reason],
                'performed_by' => $userId,
            ]);

            return $product->fresh();
        });
    }

    public function search(string $query, array $filters = [])
    {
        $q = Product::with(['store', 'user'])->where(function ($qb) use ($query) {
            $qb->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        });
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                $q->where($field, $value);
            }
        }

        return $q->latest()->paginate(15);
    }
}
