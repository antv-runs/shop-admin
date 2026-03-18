<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'rating' => (int) $this->rating,
            'comment' => $this->comment,
            'created_at' => optional($this->created_at)->format('Y-m-d'),
            'user' => [
                'name' => optional($this->user)->name ?? 'Guest',
            ],
        ];
    }
}
