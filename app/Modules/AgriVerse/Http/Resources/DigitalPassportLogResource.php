<?php

namespace App\Modules\AgriVerse\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DigitalPassportLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'product_id' => $this->product_id,
            'action' => $this->action,
            'data' => $this->data,
            'performed_by' => $this->whenLoaded('performer', fn () => [
                'id' => $this->performer->id,
                'name' => $this->performer->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
