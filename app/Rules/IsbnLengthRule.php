<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsbnLengthRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($value as $isbn) {
            $isbnLength = strlen($isbn);

            if (10 !== $isbnLength && 13 !== $isbnLength) {
                $fail("{$attribute} must have a length of 10 or 13 digits");
            }
        }
    }
}
