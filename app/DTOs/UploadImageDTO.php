<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class UploadImageDTO
{
    public int $id;
    public UploadedFile $image;

    public function __construct(int $id, UploadedFile $image)
    {
        $this->id = $id;
        $this->image = $image;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['image']
        );
    }
}
