<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'rating' => (float) $this->input('rating'),
        ]);
    }
}
