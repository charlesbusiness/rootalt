<?php

namespace Modules\ProductManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'product_category_id' => ['required', 'numeric', 'exists:product_categories,id'],
            'product_name' => ['required', 'string'],
            'product_photo' => ['required', 'array'],
            'product_photo.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:1024'], // each image max 1MB
            'product_sku' => ['required', 'string'],
            'product_retail_price' => ['required', 'numeric'],
            'product_memory_price' => ['required', 'numeric'],
            'product_qty' => ['required', 'numeric'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()?->role?->name === 'admin';
    }
}
