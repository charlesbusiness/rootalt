<?php

namespace Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CardExpirationRule implements ValidationRule
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
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if the value matches MM/YY format
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $value)) {
            return false;
        }

        // Split into month and year
        [$month, $year] = explode('/', $value);

        // Convert year to full format
        $year = intval('20' . $year);

        // Get the current year and month
        $currentYear = intval(date('Y'));
        $currentMonth = intval(date('m'));

        // Validate expiration date
        return $year > $currentYear || ($year == $currentYear && $month >= $currentMonth);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be a valid expiration date in MM/YY format and not expired.';
    }
}
