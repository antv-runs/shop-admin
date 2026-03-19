<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['required', 'string'],
            'is_verified' => ['nullable', 'boolean'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'rating' => (float) $this->input('rating'),
        ]);
    }
}
