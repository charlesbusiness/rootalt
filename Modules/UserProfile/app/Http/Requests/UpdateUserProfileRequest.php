<?php

namespace Modules\UserProfile\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'firstname' => ['sometimes', 'string'],
            'lastname' => ['sometimes', 'string'],
            'nin' => ['sometimes', 'string'],
            'gender' => ['sometimes', 'string', Rule::in(gender())],
            'country_id' => ['sometimes', 'numeric', 'exists:countries,id'],
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
