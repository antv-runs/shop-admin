<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $profileImageUrl = $this->profile_image
            ? (filter_var($this->profile_image, FILTER_VALIDATE_URL)
                ? $this->profile_image
                : Storage::url($this->profile_image))
            : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role?->value,
            'profile_image' => $profileImageUrl,
            'profile_image_url' => $profileImageUrl,
            'bio' => $this->bio,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
