<?php

namespace App\Modules\AgriVerse\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @deprecated No corresponding DigitalPassport model exists.
 */
class DigitalPassportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $specs = $this->getDefaultSpecs();

        if (is_array($this->technical_specs)) {
            $specs = array_merge($specs, $this->technical_specs);
        }

        return [
            'uuid' => $this->uuid,
            'product' => new ProductResource($this),
            'technical_specs' => $specs,
            'history' => DigitalPassportLogResource::collection($this->whenLoaded('passportLogs')),
            'certifications' => $specs['certifications'],
            'warranty_months' => $specs['warranty_months'],
        ];
    }

    private function getDefaultSpecs(): array
    {
        return [
            'engine' => null,
            'dimensions' => null,
            'performance' => null,
            'classification' => null,
            'features' => [],
            'certifications' => [],
            'warranty_months' => null,
            'year_of_manufacture' => null,
        ];
    }
}
