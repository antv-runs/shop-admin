<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:products,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID is required',
            'id.exists' => 'Resource not found',
            'image.required' => 'Image file is required',
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be jpeg, png, jpg or webp',
            'image.max' => 'Image size must not exceed 2MB',
        ];
    }
}
