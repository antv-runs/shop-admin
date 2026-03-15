<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UploadImageRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->hasFile('images[]') && !$this->hasFile('images')) {
            $this->files->set('images', $this->file('images[]'));
        }

        $images = $this->file('images');

        if ($images instanceof UploadedFile) {
            $this->files->set('images', [$images]);
        }

        if (!$this->has('images') && $this->hasFile('image')) {
            $image = $this->file('image');

            if ($image instanceof UploadedFile) {
                $this->files->set('images', [$image]);
            }
        }
    }

    public function authorize(): bool
    {
        $user = auth()->user();

        return $user !== null && ($user->role?->value === 'admin');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:products,id',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'file|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID is required',
            'id.exists' => 'Resource not found',
            'images.required' => 'At least one image file is required',
            'images.array' => 'Images must be uploaded as an array',
            'images.min' => 'At least one image file is required',
            'images.max' => 'You can upload a maximum of 10 images',
            'images.*.image' => 'Each file must be an image',
            'images.*.mimes' => 'Each image must be jpeg, png, jpg or webp',
            'images.*.max' => 'Each image size must not exceed 2MB',
        ];
    }
}
