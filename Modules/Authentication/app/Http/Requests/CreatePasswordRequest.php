<?php

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Authentication\Rules\PasswordRule;

class CreatePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => ['required_without:phone', 'email', 'exists:users,email'],
            'phone' => ['required_without:email', 'string', 'exists:users,phone'],
            'password' => ['required', 'string', new PasswordRule, 'confirmed'],
        ];
    }


    public function messages(): array
    {
        return [
            'email.required_without' => 'The email field is required when phone is not provided.',
            'phone.required_without' => 'The phone field is required when email is not provided.',
            'password.required_with' => 'The password field is required when email is provided.',
            'code.exists' => 'The provided verification code is invalid.',
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
