<?php

namespace Modules\ProductManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPlanPromotionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'product_plan_id' => ['required', 'numeric', 'exists:product_plans.id'],
            'discount' => ['required'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'gte:start_date'],
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
