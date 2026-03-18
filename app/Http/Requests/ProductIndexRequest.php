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
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'colors' => 'nullable|string',
            'sizes' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('max_price', 'gte:min_price', function ($input) {
            return $input->min_price !== null && $input->max_price !== null;
        });
    }

    public function messages()
    {
        return [
            'category_id.exists' => 'The selected category does not exist.',
            'status.in' => 'Status must be one of: active, deleted, all.',
            'per_page.max' => 'Per page value cannot exceed 100 items.',
            'min_price.numeric' => 'Minimum price must be a number.',
            'min_price.min' => 'Minimum price cannot be negative.',
            'max_price.numeric' => 'Maximum price must be a number.',
            'max_price.min' => 'Maximum price cannot be negative.',
            'max_price.gte' => 'Maximum price must be greater than or equal to minimum price.',
        ];
    }
}
