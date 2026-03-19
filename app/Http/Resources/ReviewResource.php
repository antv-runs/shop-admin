<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'comment' => $this->comment,
            'isVerified' => (bool) $this->is_verified,
            'created_at' => optional($this->created_at)->format('Y-m-d'),
            'rating' => (float) $this->rating,
            'user' => [
                'name' => optional($this->user)->name ?? 'Guest',
            ],
        ];
    }
}
