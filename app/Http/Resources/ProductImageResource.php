<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => (string) $this->id,
            'image_url'  => $this->image_url,
            'is_primary' => (bool) $this->is_primary,
            'sort_order' => (int) $this->sort_order,
            'alt_text'   => $this->alt_text,
        ];
    }
}
