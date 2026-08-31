<?php

namespace Illuminate\Validation\Rules;

use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Conditionable;
use Stringable;

class Numeric implements Stringable
{
    use Conditionable;

    /**
     * The constraints for the number rule.
     */
    protected array $constraints = ['numeric'];

    /**
     * The field under validation must have a size between the given min and max (inclusive).
     *
     * @param  int|float  $min
     * @param  int|float  $max
     * @return $this
     */
    public function between(int|float $min, int|float $max): Numeric
    {
        return $this->addRule('between:'.$min.','.$max);
    }

    /**
     * The field under validation must contain the specified number of decimal places.
     *
     * @param  int  $min
     * @param  int|null  $max
     * @return $this
     */
    public function decimal(int $min, ?int $max = null): Numeric
    {
        $rule = 'decimal:'.$min;

        if ($max !== null) {
            $rule .= ','.$max;
        }

        return $this->addRule($rule);
    }

    /**
     * The field under validation must have a different value than field.
     *
     * @param  string  $field
     * @return $this
     */
    public function different(string $field): Numeric
    {
        return $this->addRule('different:'.$field);
    }

    /**
     * The integer under validation must have an exact number of digits.
     *
     * @param  int  $length
     * @return $this
     */
    public function digits(int $length): Numeric
    {
        return $this->integer()->addRule('digits:'.$length);
    }

    /**
     * The integer under validation must between the given min and max number of digits.
     *
     * @param  int  $min
     * @param  int  $max
     * @return $this
     */
    public function digitsBetween(int $min, int $max): Numeric
    {
        return $this->integer()->addRule('digits_between:'.$min.','.$max);
    }

    /**
     * The field under validation must be greater than the given field or value.
     *
     * @param  string  $field
     * @return $this
     */
    public function greaterThan(string $field): Numeric
    {
        return $this->addRule('gt:'.$field);
    }

    /**
     * The field under validation must be greater than or equal to the given field or value.
     *
     * @param  string  $field
     * @return $this
     */
    public function greaterThanOrEqualTo(string $field): Numeric
    {
        return $this->addRule('gte:'.$field);
    }

    /**
     * The field under validation must be an integer.
     *
     * @return $this
     */
    public function integer(bool $strict = false): Numeric
    {
        return $this->addRule($strict ? 'integer:strict' : 'integer');
    }

    /**
     * The field under validation must be less than the given field.
     *
     * @param  string  $field
     * @return $this
     */
    public function lessThan(string $field): Numeric
    {
        return $this->addRule('lt:'.$field);
    }

    /**
     * The field under validation must be less than or equal to the given field.
     *
     * @param  string  $field
     * @return $this
     */
    public function lessThanOrEqualTo(string $field): Numeric
    {
        return $this->addRule('lte:'.$field);
    }

    /**
     * The field under validation must be less than or equal to a maximum value.
     *
     * @param  int|float  $value
     * @return $this
     */
    public function max(int|float $value): Numeric
    {
        return $this->addRule('max:'.$value);
    }

    /**
     * The integer under validation must have a maximum number of digits.
     *
     * @param  int  $value
     * @return $this
     */
    public function maxDigits(int $value): Numeric
    {
        return $this->addRule('max_digits:'.$value);
    }

    /**
     * The field under validation must have a minimum value.
     *
     * @param  int|float  $value
     * @return $this
     */
    public function min(int|float $value): Numeric
    {
        return $this->addRule('min:'.$value);
    }

    /**
     * The integer under validation must have a minimum number of digits.
     *
     * @param  int  $value
     * @return $this
     */
    public function minDigits(int $value): Numeric
    {
        return $this->addRule('min_digits:'.$value);
    }

    /**
     * The field under validation must be a multiple of the given value.
     *
     * @param  int|float  $value
     * @return $this
     */
    public function multipleOf(int|float $value): Numeric
    {
        return $this->addRule('multiple_of:'.$value);
    }

    /**
     * The given field must match the field under validation.
     *
     * @param  string  $field
     * @return $this
     */
    public function same(string $field): Numeric
    {
        return $this->addRule('same:'.$field);
    }

    /**
     * The field under validation must match the given value.
     *
     * @param  int  $value
     * @return $this
     */
    public function exactly(int $value): Numeric
    {
        return $this->integer()->addRule('size:'.$value);
    }

    /**
     * Convert the rule to a validation string.
     */
    public function __toString(): string
    {
        return implode('|', array_unique($this->constraints));
    }

    /**
     * Add custom rules to the validation rules array.
     */
    protected function addRule(array|string $rules): Numeric
    {
        $this->constraints = array_merge($this->constraints, Arr::wrap($rules));

        return $this;
    }
}
