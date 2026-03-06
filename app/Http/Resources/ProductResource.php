<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => (float) $this->price,
            'description' => $this->description,
            'category' => $this->whenLoaded('category', function () {
                return new CategoryResource($this->category);
            }),
            'image' => $this->image,
            'image_url' => $this->image_url,
            'created_at' => optional($this->created_at)->toDateTimeString(),
        ];
    }
}
