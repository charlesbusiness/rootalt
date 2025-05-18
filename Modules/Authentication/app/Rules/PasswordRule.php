<?php

namespace Modules\Authentication\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PasswordRule implements ValidationRule
{
    protected $value;
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->passes($attribute, $value)) {
            $fail($this->message($value));
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if the password meets your custom criteria
        return (bool) preg_match('/[A-Z]/', $value) && // At least one uppercase letter
            preg_match('/[a-z]/', $value) && // At least one lowercase letter
            preg_match('/[0-9]/', $value) && // At least one digit
            preg_match('/[@$!#%*?&]/', $value) && // At least one special character
            strlen($value) >= 8 && // Minimum length of 8 characters
            stripos($value, 'password') === false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message($value)
    {
        if (stripos($value, 'password') !== false) {
            return 'The :attribute must not contain the word "password".';
        }

        return 'The :attribute must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.';
    }
}
