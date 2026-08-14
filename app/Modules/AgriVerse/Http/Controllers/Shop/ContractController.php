<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use App\Modules\AgriVerse\Models\Contract;
use Inertia\Inertia;

class ContractController
{
    public function show(Contract $contract)
    {
        $user = auth()->user();
        if ($contract->order->buyer_id !== $user->id && $contract->order->seller_id !== $user->id && ! $user->hasRole('admin')) {
            abort(403);
        }

        $contract->load(['order.product', 'order.buyer', 'order.seller']);

        return Inertia::render('Marketplace/Contracts/Show', ['contract' => $contract]);
    }
}
