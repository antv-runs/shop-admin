<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Product form request validation
 *
 * Single Responsibility: validates product data only
 * Improves maintainability and follows SOLID principles
 */
class ProductRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // no-op
    }

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
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            ];
        }

        // PUT/PATCH
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|gte:price',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'images.array' => 'Images must be uploaded as an array.',
            'images.*.image' => 'Each selected file must be a valid image.',
            'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, webp.',
            'images.*.max' => 'Each image may not be greater than 2MB.',
        ];
    }
}
