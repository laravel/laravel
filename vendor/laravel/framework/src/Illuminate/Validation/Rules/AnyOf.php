<?php

namespace Illuminate\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class AnyOf implements Rule, ValidatorAwareRule
{
    /**
     * The rules to match against.
     *
     * @var array
     */
    protected array $rules = [];

    /**
     * The validator performing the validation.
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Sets the validation rules to match against.
     *
     * @param  array  $rules
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($rules)
    {
        if (! is_array($rules)) {
            throw new InvalidArgumentException('The provided value must be an array of validation rules.');
        }

        $this->rules = $rules;
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
        foreach ($this->rules as $rule) {
            $validator = Validator::make(
                Arr::isAssoc(Arr::wrap($value)) ? $value : [$value],
                Arr::isAssoc(Arr::wrap($rule)) ? $rule : [$rule],
                $this->validator->customMessages,
                $this->validator->customAttributes
            );

            if ($validator->passes()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function message()
    {
        $message = $this->validator->getTranslator()->get('validation.any_of');

        return $message === 'validation.any_of'
            ? ['The :attribute field is invalid.']
            : $message;
    }

    /**
     * Set the current validator.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }
}
