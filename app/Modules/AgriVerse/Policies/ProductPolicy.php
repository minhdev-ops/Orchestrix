<?php

namespace App\Modules\AgriVerse\Policies;

use App\Models\User;
use App\Modules\AgriVerse\Models\Product;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['seller', 'admin']);
    }

    public function update(User $user, Product $product): bool
    {
        return $product->user_id === $user->id
            || $user->hasAnyRole(['admin', 'employee']);
    }

    public function delete(User $user, Product $product): bool
    {
        return $product->user_id === $user->id
            || $user->hasRole('admin');
    }
}
