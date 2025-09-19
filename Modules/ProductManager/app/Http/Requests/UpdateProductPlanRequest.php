<?php

namespace Modules\ProductManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductPlanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'numeric', 'exists:product_plans,id'],
            'plan_name' => ['sometimes', 'string', 'min:2'],
            'plan_id' => ['sometimes', 'numeric', 'exists:product_plans,id'],
            'plan_price' => ['sometimes', 'numeric', 'min:0'],
            'level_1_percentage' => ['sometimes', 'numric'],
            'level_2_percentage' => ['sometimes', 'numric'],
            'level_3_percentage' => ['sometimes', 'numric'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
