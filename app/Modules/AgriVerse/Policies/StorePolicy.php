<?php

namespace App\Modules\AgriVerse\Policies;

use App\Models\User;
use App\Modules\AgriVerse\Models\Store;

class StorePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Store $store): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('seller');
    }

    public function update(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id
            || $user->hasAnyRole(['admin', 'employee']);
    }

    public function delete(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id
            || $user->hasRole('admin');
    }
}
