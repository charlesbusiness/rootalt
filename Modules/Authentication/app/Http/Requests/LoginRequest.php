<?php

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => ['required_without:username', 'email'],
            'username' => ['required_without:email', 'string', 'exists:users,username'],
            'code' => ['nullable', 'string', 'exists:otp_managers,code'],
            'password' => ['required', 'string'],
        ];
    }


    public function messages(): array
    {
        return [
            'email.required_without' => 'The email field is required when phone is not provided.',
            'phone.required_without' => 'The phone field is required when email is not provided.',
            'password.required_with' => 'The password field is required when logging in with email.',
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
