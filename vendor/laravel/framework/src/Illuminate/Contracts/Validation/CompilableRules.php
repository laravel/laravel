<?php

namespace Illuminate\Contracts\Validation;

interface CompilableRules
{
    /**
     * Compile the object into usable rules.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $data
     * @param  mixed  $context
     * @return \stdClass
     */
    public function compile($attribute, $value, $data = null, $context = null);
}
