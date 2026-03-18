<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'customer' => ['required', 'array'],
            'customer.name' => ['required', 'string', 'max:255'],
            'customer.email' => ['required', 'email', 'max:255'],
            'customer.phone' => ['required', 'string', 'max:50'],
            'customer.address' => ['required', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.color' => ['nullable', 'string', 'max:50'],
            'items.*.size' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages()
    {
        return [
            'customer.required' => 'Customer information is required.',
            'customer.name.required' => 'Recipient name is required.',
            'customer.email.required' => 'Recipient email is required.',
            'customer.phone.required' => 'Recipient phone is required.',
            'customer.address.required' => 'Shipping address is required.',
            'items.required' => 'You must add at least one item to the order.',
            'items.*.product_id.exists' => 'Selected product does not exist.',
        ];
    }
}
