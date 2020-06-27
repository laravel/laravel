<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FilePathRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match('/^(\/)?([A-z0-9-_+]+\/)*([A-z0-9]+(\..+)?)$/im', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not valid';
    }
}
