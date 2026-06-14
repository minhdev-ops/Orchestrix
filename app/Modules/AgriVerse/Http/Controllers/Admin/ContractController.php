<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Modules\AgriVerse\Models\Contract;

class ContractController
{
    public function index(Request $request)
    {
        $query = Contract::with(['order.product', 'order.buyer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contracts = $query->latest()->paginate(15);

        return Inertia::render('Admin/Contracts/Index', [
            'contracts' => $contracts,
        ]);
    }

    public function show(Contract $contract)
    {
        $contract->load(['order.product', 'order.buyer', 'order.seller']);

        return Inertia::render('Admin/Contracts/Show', [
            'contract' => $contract,
        ]);
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('admin.agriverse.contracts.index')
            ->with('success', 'Hợp đồng đã được xóa.');
    }
}
