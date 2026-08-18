<?php

namespace App\Modules\AgriVerse\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'order' => $this->whenLoaded('order', fn () => [
                'id' => $this->order->id,
                'uuid' => $this->order->uuid,
                'product' => $this->order->product?->name,
                'total_price' => $this->order->total_price,
                'status' => $this->order->status,
            ]),
            'amount' => $this->amount,
            'commission_fee' => $this->commission_fee,
            'seller_amount' => $this->seller_amount,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
        ];
    }
}
