<?php

namespace App\Modules\AgriVerse\Http\Controllers\Shop;

use Inertia\Inertia;
use App\Modules\AgriVerse\Models\Contract;

class ContractController
{
    public function show(Contract $contract)
    {
        $contract->load(['order.product', 'order.buyer', 'order.seller']);
        return Inertia::render('Marketplace/Contracts/Show', ['contract' => $contract]);
    }
}
