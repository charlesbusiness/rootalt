<?php

namespace Modules\ProductManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductPlanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'plan_name' => ['required', 'string', 'min:2'],
            'plan_id' => ['required', 'numeric', 'exists:product_plans,id'],
            'plan_price' => ['required', 'numeric', 'min:0'],
            'level_1_percentage' => ['required', 'numric'],
            'level_2_percentage' => ['required', 'numric'],
            'level_3_percentage' => ['required', 'numric'],
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
