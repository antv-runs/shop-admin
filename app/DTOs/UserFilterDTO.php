<?php

namespace App\DTOs;

use App\Http\Requests\UserIndexRequest;

class UserFilterDTO
{
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $status = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
    ) {}

    public static function fromRequest(UserIndexRequest $request): self
    {
        $status = $request->input('status');

        return new self(
            search: $request->input('search'),
            status: $status === 'trashed' ? 'deleted' : $status,
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
        );
    }
}
