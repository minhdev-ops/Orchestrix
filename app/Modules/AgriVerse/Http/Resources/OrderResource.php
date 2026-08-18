<?php

namespace App\Modules\AgriVerse\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'product' => new ProductResource($this->whenLoaded('product')),
            'buyer' => $this->whenLoaded('buyer', fn () => [
                'id' => $this->buyer->id,
                'name' => $this->buyer->name,
                'email' => $this->buyer->email,
            ]),
            'seller' => $this->whenLoaded('seller', fn () => [
                'id' => $this->seller->id,
                'name' => $this->seller->name,
                'email' => $this->seller->email,
            ]),
            'store' => new StoreResource($this->whenLoaded('store')),
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'total_price' => (float) $this->total_price,
            'discount_amount' => (float) $this->discount_amount,
            'total_amount' => (float) $this->total_amount,
            'commission_fee' => (float) $this->commission_fee,
            'status' => $this->status,
            'shipping_address' => $this->shipping_address,
            'notes' => $this->notes,
            'contract' => new ContractResource($this->whenLoaded('contract')),
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
            'statuses' => $this->whenLoaded('statuses', fn () => $this->statuses->map(fn ($s) => [
                'status' => $s->status,
                'note' => $s->note,
                'created_at' => $s->created_at,
            ])),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
