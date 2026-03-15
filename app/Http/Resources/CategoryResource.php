<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'href' => '/shop/' . ($this->slug ?? $this->id),
            'hasChildren' => (bool) ($this->children_count ?? 0),
        ];
    }
}
