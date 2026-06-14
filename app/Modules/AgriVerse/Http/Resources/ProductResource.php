<?php

namespace App\Modules\AgriVerse\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'compare_price' => (float) $this->compare_price,
            'category' => $this->category,
            'tags' => $this->tags,
            'status' => $this->status,
            'store_id' => $this->store_id,
            'stock' => $this->stock,
            'user' => new \App\Http\Resources\UserResource($this->whenLoaded('user')),
            'store' => new StoreResource($this->whenLoaded('store')),
            'assets' => ThreeDAssetResource::collection($this->whenLoaded('assets')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
