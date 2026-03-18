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
        if ($this->has('colors_input')) {
            $this->merge([
                'colors' => array_filter(array_map('trim', explode(',', $this->colors_input ?? ''))),
            ]);
        }

        if ($this->has('sizes_input')) {
            $this->merge([
                'sizes' => array_filter(array_map('trim', explode(',', $this->sizes_input ?? ''))),
            ]);
        }
    }

    public function authorize()
    {
        $user = $this->user();

        return $user !== null
            && method_exists($user, 'isAdmin')
            && $user->isAdmin();
    }

    public function rules()
    {
        if ($this->isMethod('post')) {
            return [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'compare_price' => 'nullable|numeric|gte:price',
                'description' => 'nullable|string',
                'details' => 'nullable|string',
                'colors' => 'nullable|array',
                'colors.*' => 'nullable|string',
                'sizes' => 'nullable|array',
                'sizes.*' => 'nullable|string',
                'currency' => 'required|string|size:3',
                'is_active' => 'nullable|boolean',
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
            'details' => 'nullable|string',
            'colors' => 'nullable|array',
            'colors.*' => 'nullable|string',
            'sizes' => 'nullable|array',
            'sizes.*' => 'nullable|string',
            'currency' => 'required|string|size:3',
            'is_active' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'currency.size' => 'Currency must be a 3-letter ISO code (e.g. USD).',
            'colors.array' => 'Colors must be provided as a list.',
            'colors.*.string' => 'Each color must be a valid text value.',
            'sizes.array' => 'Sizes must be provided as a list.',
            'sizes.*.string' => 'Each size must be a valid text value.',
            'images.array' => 'Images must be uploaded as an array.',
            'images.*.image' => 'Each selected file must be a valid image.',
            'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, webp.',
            'images.*.max' => 'Each image may not be greater than 2MB.',
        ];
    }
}
