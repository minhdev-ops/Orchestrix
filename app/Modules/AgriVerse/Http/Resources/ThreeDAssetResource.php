<?php

namespace App\Modules\AgriVerse\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreeDAssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'original_filename' => $this->original_filename,
            'original_path' => $this->original_path,
            'compressed_filename' => $this->compressed_filename,
            'compressed_path' => $this->compressed_path,
            'format' => $this->format,
            'asset_type' => $this->asset_type,
            'compression_status' => $this->compression_status,
            'file_size' => $this->file_size,
            'compressed_file_size' => $this->compressed_file_size,
            'thumbnail_path' => $this->thumbnail_path,
            'product' => new ProductResource($this->whenLoaded('product')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
