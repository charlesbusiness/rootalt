<?php

namespace Modules\UserProfile\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Authentication\Rules\PasswordRule;

class UpdateUserPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', new PasswordRule, 'confirmed'],
            'old_password' => ['required', 'string'],
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
