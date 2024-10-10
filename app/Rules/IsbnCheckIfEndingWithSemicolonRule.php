<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class IsbnCheckIfEndingWithSemicolonRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isbnArray = explode(';', $value);

        foreach ($isbnArray as $isbn) {
            if (empty($isbn)) {
                $fail("{$attribute} cannot end with a semicolon");
            }
        }

    }
}
