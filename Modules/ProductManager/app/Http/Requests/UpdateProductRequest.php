<?php

namespace Modules\ProductManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'product_category_id' => ['sometimes', 'numeric', 'exists:product_categories,id'],
            'id' => ['required', 'numeric', 'exists:products,id'],
            'product_name' => ['sometimes', 'filled', 'string'],
            'product_photo' => ['sometimes', 'filled', 'array'],
            'product_photo.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:1024'], // each image max 1MB
            'product_sku' => ['sometimes', 'filled', 'string'],
            'product_retail_price' => ['sometimes', 'filled', 'numeric'],
            'product_memory_price' => ['sometimes', 'filled', 'numeric'],
            'product_qty' => ['sometimes', 'filled', 'numeric'],
            'movement_ype' => ['required',  'string', 'in:inbound,outbound'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        if (Auth::user()->role->name === 'admin') {
            return true;
        }
        return false;
    }
}
