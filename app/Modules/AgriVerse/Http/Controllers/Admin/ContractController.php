<?php

namespace App\Modules\AgriVerse\Http\Controllers\Admin;

use App\Modules\AgriVerse\Models\Contract;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

    public function destroy(Request $request, Contract $contract)
    {
        $order = $contract->order;

        abort_unless($order, 404, 'Đơn hàng liên kết không tồn tại.');
        abort_unless($order->store_id, 422, 'Hợp đồng không thuộc về cửa hàng nào.');

        $user = auth()->user();
        abort_unless(
            $user->isAdmin() || $order->buyer_id === $user->id || $order->seller_id === $user->id,
            403,
            'Bạn không có quyền xóa hợp đồng này.'
        );

        $contract->delete();

        return redirect()->route('admin.agriverse.contracts.index')
            ->with('success', 'Hợp đồng đã được xóa.');
    }
}
