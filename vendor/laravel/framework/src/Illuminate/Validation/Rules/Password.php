<?php

namespace Illuminate\Validation\Rules;

use ArrayIterator;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ImplicitRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\UncompromisedVerifier;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Traits\Conditionable;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

class Password implements DataAwareRule, ImplicitRule, IteratorAggregate, Rule, ValidatorAwareRule
{
    use Conditionable;

    /**
     * The validator performing the validation.
     *
     * @var \Illuminate\Contracts\Validation\Validator
     */
    protected $validator;

    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data;

    /**
     * The minimum size of the password.
     *
     * @var int
     */
    protected $min = 8;

    /**
     * The maximum size of the password.
     *
     * @var int
     */
    protected $max;

    /**
     * If the password is required.
     *
     * @var bool
     */
    protected $required = false;

    /**
     * If the password should only be validated when present.
     *
     * @var bool
     */
    protected $sometimes = false;

    /**
     * If the password requires at least one uppercase and one lowercase letter.
     *
     * @var bool
     */
    protected $mixedCase = false;

    /**
     * If the password requires at least one letter.
     *
     * @var bool
     */
    protected $letters = false;

    /**
     * If the password requires at least one number.
     *
     * @var bool
     */
    protected $numbers = false;

    /**
     * If the password requires at least one symbol.
     *
     * @var bool
     */
    protected $symbols = false;

    /**
     * If the password should not have been compromised in data leaks.
     *
     * @var bool
     */
    protected $uncompromised = false;

    /**
     * The number of times a password can appear in data leaks before being considered compromised.
     *
     * @var int
     */
    protected $compromisedThreshold = 0;

    /**
     * Additional validation rules that should be merged into the default rules during validation.
     *
     * @var array
     */
    protected $customRules = [];

    /**
     * The failure messages, if any.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * The callback that will generate the "default" version of the password rule.
     *
     * @var string|array|callable|null
     */
    public static $defaultCallback;

    /**
     * Create a new rule instance.
     *
     * @param  int  $min
     */
    public function __construct($min)
    {
        $this->min = max((int) $min, 1);
    }

    /**
     * Set the default callback to be used for determining a password's default rules.
     *
     * If no arguments are passed, the default password rule configuration will be returned.
     *
     * @param  static|callable|null  $callback
     * @return static|void
     *
     * @throws \InvalidArgumentException
     */
    public static function defaults($callback = null)
    {
        if (is_null($callback)) {
            return static::default();
        }

        if (! is_callable($callback) && ! $callback instanceof static) {
            throw new InvalidArgumentException('The given callback should be callable or an instance of '.static::class);
        }

        static::$defaultCallback = $callback;
    }

    /**
     * Get the default configuration of the password rule.
     *
     * @return static
     */
    public static function default()
    {
        $password = is_callable(static::$defaultCallback)
            ? call_user_func(static::$defaultCallback)
            : static::$defaultCallback;

        return $password instanceof Rule ? $password : static::min(8);
    }

    /**
     * Get the default configuration of the password rule and mark the field as required.
     *
     * @return static
     */
    public static function required()
    {
        $password = static::default();

        $password->required = true;

        return $password;
    }

    /**
     * Get the default configuration of the password rule and mark the field as sometimes being required.
     *
     * @return static
     */
    public static function sometimes()
    {
        $password = static::default();

        $password->sometimes = true;

        return $password;
    }

    /**
     * Set the performing validator.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
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
     * Set the minimum size of the password.
     *
     * @param  int  $size
     * @return $this
     */
    public static function min($size)
    {
        return new static($size);
    }

    /**
     * Set the maximum size of the password.
     *
     * @param  int  $size
     * @return $this
     */
    public function max($size)
    {
        $this->max = $size;

        return $this;
    }

    /**
     * Ensures the password has not been compromised in data leaks.
     *
     * @param  int  $threshold
     * @return $this
     */
    public function uncompromised($threshold = 0)
    {
        $this->uncompromised = true;

        $this->compromisedThreshold = $threshold;

        return $this;
    }

    /**
     * Makes the password require at least one uppercase and one lowercase letter.
     *
     * @return $this
     */
    public function mixedCase()
    {
        $this->mixedCase = true;

        return $this;
    }

    /**
     * Makes the password require at least one letter.
     *
     * @return $this
     */
    public function letters()
    {
        $this->letters = true;

        return $this;
    }

    /**
     * Makes the password require at least one number.
     *
     * @return $this
     */
    public function numbers()
    {
        $this->numbers = true;

        return $this;
    }

    /**
     * Makes the password require at least one symbol.
     *
     * @return $this
     */
    public function symbols()
    {
        $this->symbols = true;

        return $this;
    }

    /**
     * Specify additional validation rules that should be merged with the default rules during validation.
     *
     * @param  \Closure|string|array  $rules
     * @return $this
     */
    public function rules($rules)
    {
        $this->customRules = Arr::wrap($rules);

        return $this;
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
        $this->messages = [];

        if (! $this->required && ! $this->sometimes && ! Arr::has($this->data ?? [], $attribute)) {
            return true;
        }

        if (blank($value) && ! $this->required && $this->validator?->hasRule($attribute, ['Nullable'])) {
            return true;
        }

        $validator = Validator::make(
            $this->data,
            [$attribute => [...$this]],
            $this->validator->customMessages,
            $this->validator->customAttributes
        )->after(function ($validator) use ($attribute, $value) {
            if (! is_string($value)) {
                return;
            }

            if ($this->mixedCase && ! preg_match('/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/u', $value)) {
                $validator->addFailure($attribute, 'password.mixed');
            }

            if ($this->letters && ! preg_match('/\pL/u', $value)) {
                $validator->addFailure($attribute, 'password.letters');
            }

            if ($this->symbols && ! preg_match('/\p{Z}|\p{S}|\p{P}/u', $value)) {
                $validator->addFailure($attribute, 'password.symbols');
            }

            if ($this->numbers && ! preg_match('/\pN/u', $value)) {
                $validator->addFailure($attribute, 'password.numbers');
            }
        });

        if ($validator->fails()) {
            return $this->fail($validator->messages()->all());
        }

        if ($this->uncompromised && ! Container::getInstance()->make(UncompromisedVerifier::class)->verify([
            'value' => $value,
            'threshold' => $this->compromisedThreshold,
        ])) {
            $validator->addFailure($attribute, 'password.uncompromised');

            return $this->fail($validator->messages()->all());
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return array
     */
    public function message()
    {
        return $this->messages;
    }

    /**
     * Adds the given failures, and return false.
     *
     * @param  array|string  $messages
     * @return bool
     */
    protected function fail($messages)
    {
        $this->messages = array_merge($this->messages, Arr::wrap($messages));

        return false;
    }

    /**
     * Get information about the current state of the password validation rules.
     *
     * @return array
     */
    public function appliedRules()
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'mixedCase' => $this->mixedCase,
            'letters' => $this->letters,
            'numbers' => $this->numbers,
            'symbols' => $this->symbols,
            'uncompromised' => $this->uncompromised,
            'compromisedThreshold' => $this->compromisedThreshold,
            'customRules' => $this->customRules,
        ];
    }

    /**
     * Get an iterator for the password validation rules.
     *
     * @return \ArrayIterator<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator([
            ...($this->required ? ['required'] : []),
            ...($this->sometimes ? ['sometimes'] : []),
            'string',
            'min:'.$this->min,
            ...($this->max ? ['max:'.$this->max] : []),
            ...$this->customRules,
        ]);
    }
}
