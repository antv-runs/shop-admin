<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductImageResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $currentPrice = (float) ($this->price ?? 0);
        $originalPrice = $this->compare_price !== null
            ? (float) $this->compare_price
            : $currentPrice;
        $fallbackImage = $this->relationLoaded('images') ? $this->images->first() : null;
        $thumbnailPath = optional($this->primaryImage)->image_url ?: optional($fallbackImage)->image_url;
        $thumbnailUrl = $thumbnailPath
            ? Storage::url($thumbnailPath)
            : null;

        $discountPercent = null;
        if ($originalPrice > $currentPrice && $originalPrice > 0) {
            $discountPercent = (int) round((($originalPrice - $currentPrice) / $originalPrice) * 100);
        }

        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'pricing' => [
                'currency' => $this->currency ?? 'USD',
                'current' => $currentPrice,
                'original' => $originalPrice,
                'discountPercent' => $discountPercent,
            ],
            'thumbnail' => $thumbnailUrl,
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => (string) $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }, []),
        ];
    }
}
