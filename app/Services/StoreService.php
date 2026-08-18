<?php

namespace App\Services;

use App\Modules\AgriVerse\Models\Store;

class StoreService extends BaseService
{
    protected function modelClass(): string
    {
        return Store::class;
    }

    public function getStoresForUser(int $userId)
    {
        return Store::withCount('products')->where('owner_id', $userId)->latest()->paginate(15);
    }

    public function search(string $query)
    {
        return Store::with('owner')->withCount('products')
            ->where('name', 'like', "%{$query}%")
            ->latest()->paginate(15);
    }
}
