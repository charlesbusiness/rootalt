<?php

namespace Modules\UserProfile\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Authentication\Rules\PasswordRule;

class Set2FARequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'boolean'],
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
