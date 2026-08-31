<?php

namespace Illuminate\Validation;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Translation\CreatesPotentiallyTranslatedStrings;

class InvokableValidationRule implements Rule, ValidatorAwareRule
{
    use CreatesPotentiallyTranslatedStrings;

    /**
     * The invokable that validates the attribute.
     *
     * @var \Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Contracts\Validation\InvokableRule
     */
    protected $invokable;

    /**
     * Indicates if the validation invokable failed.
     *
     * @var bool
     */
    protected $failed = false;

    /**
     * The validation error messages.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * The current validator.
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Create a new explicit Invokable validation rule.
     *
     * @param  \Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Contracts\Validation\InvokableRule  $invokable
     */
    protected function __construct(ValidationRule|InvokableRule $invokable)
    {
        $this->invokable = $invokable;
    }

    /**
     * Create a new implicit or explicit Invokable validation rule.
     *
     * @param  \Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Contracts\Validation\InvokableRule  $invokable
     * @return \Illuminate\Validation\InvokableValidationRule
     */
    public static function make($invokable)
    {
        if ($invokable->implicit ?? false) {
            return new class($invokable) extends InvokableValidationRule implements ImplicitRule {
            };
        }

        return new InvokableValidationRule($invokable);
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
        $this->failed = false;

        if ($this->invokable instanceof DataAwareRule) {
            $this->invokable->setData($this->validator->getData());
        }

        if ($this->invokable instanceof ValidatorAwareRule) {
            $this->invokable->setValidator($this->validator);
        }

        $method = $this->invokable instanceof ValidationRule
            ? 'validate'
            : '__invoke';

        $this->invokable->{$method}($attribute, $value, function ($attribute, $message = null) {
            $this->failed = true;

            return $this->pendingPotentiallyTranslatedString($attribute, $message);
        });

        return ! $this->failed;
    }

    /**
     * Get the underlying invokable rule.
     *
     * @return \Illuminate\Contracts\Validation\ValidationRule|\Illuminate\Contracts\Validation\InvokableRule
     */
    public function invokable()
    {
        return $this->invokable;
    }

    /**
     * Get the validation error messages.
     *
     * @return array
     */
    public function message()
    {
        return $this->messages;
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the current validator.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }
}
