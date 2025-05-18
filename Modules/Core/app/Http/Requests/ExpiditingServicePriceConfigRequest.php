<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpiditingServicePriceConfigRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [

            'service_id' => ['required', 'numeric', 'exists:sub_service_data,id'],
            'vehicle_category_id' => ['nullable', 'exists:vehicle_categories,id'],
            'activity_type' => ['nullable', 'string', 'in:registration,renewal'],
            'service_cost' => ['required', 'numeric', 'min:1'],
            'transaction_fee' => ['required', 'numeric', 'min:1'],
            'service_charge' => ['nullable', 'numeric', 'min:1'],
            'dollar_rate' => ['nullable', 'numeric', 'min:1'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            'category_name.required' => 'The category name is required.',
            'category_name.string' => 'The category name must be a valid string.',

            'currency.string' => 'The currency must be a valid string.',
            'currency.in' => 'The currency must be either USD or LBD.',

            'is_registration.boolean' => 'The registration status must be true or false.',

            'category_cost.required' => 'The category cost is required.',
            'category_cost.numeric' => 'The category cost must be a numeric value.',
            'category_cost.min' => 'The category cost must be at least 0.',

            'category_service_charge.required' => 'The category service charge is required.',
            'category_service_charge.numeric' => 'The category service charge must be a numeric value.',
            'category_service_charge.min' => 'The category service charge must be at least 0.',

            'category_details.string' => 'The category details must be a valid string.',
            'category_details.min' => 'The category details must be at least 10 characters long.',
        ];
    }
}
