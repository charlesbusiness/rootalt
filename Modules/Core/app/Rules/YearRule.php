<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class YearRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
    
    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        $currentYear = date('Y');

        return is_numeric($value) 
            && strlen($value) === 4 
            && (int) $value >= 1900 
            && (int) $value <= $currentYear;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'The :attribute must be a valid year between 1900 and the current year.';
    }
}

