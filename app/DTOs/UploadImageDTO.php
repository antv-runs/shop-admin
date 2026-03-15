<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class UploadImageDTO
{
    public int $id;
    /**
     * @var UploadedFile[]
     */
    public array $images;

    public function __construct(int $id, array $images)
    {
        $this->id = $id;
        $this->images = $images;
    }

    public static function fromArray(array $data): self
    {
        $images = $data['images'] ?? [];

        if ($images instanceof UploadedFile) {
            $images = [$images];
        }

        return new self(
            $data['id'],
            $images
        );
    }
}
