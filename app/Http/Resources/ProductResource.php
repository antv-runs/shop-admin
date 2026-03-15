<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $currentPrice = (float) ($this->price ?? 0);
        $originalPrice = $this->compare_price !== null
            ? (float) $this->compare_price
            : $currentPrice;

        $discountPercent = null;
        if ($originalPrice > $currentPrice && $originalPrice > 0) {
            $discountPercent = (int) round((($originalPrice - $currentPrice) / $originalPrice) * 100);
        }

        $images = $this->whenLoaded('images', function () {
            return $this->images
                ->pluck('image_url')
                ->filter()
                ->values()
                ->all();
        }, []);

        $image = $this->image_url;

        if (empty($images) && !empty($image)) {
            $images = [$image];
        }

        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'pricing' => [
                'currency' => $this->currency ?? 'USD',
                'current' => $currentPrice,
                'original' => $originalPrice,
                'discountPercent' => $discountPercent,
            ],
            'image' => $image,
            'images' => $images,
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
