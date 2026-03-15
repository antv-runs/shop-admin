<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductApiRequest extends FormRequest
{
    public function authorize()
    {
        $user = auth()->user();

        return $user !== null && $user->role === 'admin';
    }

    public function rules()
    {
        if ($this->isMethod('post')) {
            return [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'compare_price' => 'nullable|numeric|gte:price',
                'description' => 'nullable|string',
                'category_id' => 'nullable|exists:categories,id',
            ];
        }

        // PUT/PATCH
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|gte:price',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }
}
