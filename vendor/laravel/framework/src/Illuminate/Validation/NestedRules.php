<?php

namespace Illuminate\Validation;

use Illuminate\Contracts\Validation\CompilableRules;

class NestedRules implements CompilableRules
{
    /**
     * The callback to execute.
     *
     * @var callable
     */
    protected $callback;

    /**
     * Create a new nested rule instance.
     *
     * @param  callable  $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Compile the callback into an array of rules.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  mixed  $data
     * @param  mixed  $context
     * @return \stdClass
     */
    public function compile($attribute, $value, $data = null, $context = null)
    {
        $rules = call_user_func($this->callback, $value, $attribute, $data, $context);

        return Rule::compile($attribute, $rules, $data);
    }
}
