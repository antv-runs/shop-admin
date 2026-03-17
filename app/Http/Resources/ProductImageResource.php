<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="ProductImage",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="1"),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="https://cdn.example.com/products/image.jpg"),
 *     @OA\Property(property="is_primary", type="boolean", example=true),
 *     @OA\Property(property="sort_order", type="integer", example=0),
 *     @OA\Property(property="alt_text", type="string", nullable=true, example="Front view of product")
 * )
 */
class ProductImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => (string) $this->id,
            'image_url'  => $this->image_url
                ? Storage::url($this->image_url)
                : null,
            'is_primary' => (bool) $this->is_primary,
            'sort_order' => (int) $this->sort_order,
            'alt_text'   => $this->alt_text,
        ];
    }
}
