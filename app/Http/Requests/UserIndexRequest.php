<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function authorize()
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        return $user && method_exists($user, 'isAdmin') && $user->isAdmin();
    }

    public function rules()
    {
        return [
            'search' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
