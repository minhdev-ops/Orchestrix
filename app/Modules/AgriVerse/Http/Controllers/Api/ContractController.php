<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Http\Requests\StoreContractRequest;
use App\Modules\AgriVerse\Http\Resources\ContractResource;
use App\Modules\AgriVerse\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class ContractController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $query = Contract::query()->with('order');

        if ($user->hasRole('buyer')) {
            $query->whereHas('order', fn ($q) => $q->where('buyer_id', $user->id));
        } elseif ($user->hasRole('seller')) {
            $query->whereHas('order', fn ($q) => $q->where('seller_id', $user->id));
        }

        return ContractResource::collection($query->latest()->paginate($request->per_page ?? 15));
    }

    public function show(Contract $contract): ContractResource
    {
        return ContractResource::make($contract->load('order.product', 'order.buyer', 'order.seller'));
    }

    public function store(StoreContractRequest $request): ContractResource
    {
        $contract = Contract::create([
            'order_id' => $request->order_id,
            'contract_number' => 'CTR-'.strtoupper(Str::random(10)),
            'content' => $request->content,
            'status' => 'draft',
        ]);

        return ContractResource::make($contract->load('order'));
    }

    public function sign(Request $request, Contract $contract)
    {
        $user = $request->user();
        $order = $contract->order;

        if ($user->id === $order->buyer_id) {
            $contract->update(['signed_by_buyer' => true]);
        } elseif ($user->id === $order->seller_id) {
            $contract->update(['signed_by_seller' => true]);
        } else {
            abort(403, 'Forbidden');
        }

        if ($contract->signed_by_buyer && $contract->signed_by_seller) {
            $contract->update([
                'status' => 'signed',
                'signed_at' => now(),
                'content_hash' => hash('sha256', $contract->content),
            ]);
        }

        return ContractResource::make($contract->load('order'));
    }

    public function downloadPdf(Contract $contract)
    {
        $contract->load('order.product', 'order.buyer', 'order.seller');

        $pdf = Pdf::loadView('agriverse::contract-pdf', [
            'contract' => $contract,
        ]);

        return $pdf->download("contract_{$contract->contract_number}.pdf");
    }
}
