<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50',
            'rating' => 'nullable|numeric|min:1|max:5',
            'sort' => 'nullable|in:latest,oldest,highest_rating',
        ];
    }
}
