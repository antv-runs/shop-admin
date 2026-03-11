<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,deleted,all,trashed',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages()
    {
        return [
            'status.in' => 'Status must be one of: active, deleted, all.',
            'per_page.max' => 'Per page value cannot exceed 100 items.',
        ];
    }
}
