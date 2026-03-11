<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
            'status' => 'nullable|string|in:active,deleted,all,trashed',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages()
    {
        return [
            'category_id.exists' => 'The selected category does not exist.',
            'status.in' => 'Status must be one of: active, deleted, all.',
            'per_page.max' => 'Per page value cannot exceed 100 items.',
        ];
    }
}
