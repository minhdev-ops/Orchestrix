<?php

namespace App\Modules\AgriVerse\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'contract_number' => $this->contract_number,
            'order' => new OrderResource($this->whenLoaded('order')),
            'content' => $this->content,
            'status' => $this->status,
            'signed_by_buyer' => $this->signed_by_buyer,
            'signed_by_seller' => $this->signed_by_seller,
            'signed_at' => $this->signed_at,
            'content_hash' => $this->content_hash,
            'file_path' => $this->file_path,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
