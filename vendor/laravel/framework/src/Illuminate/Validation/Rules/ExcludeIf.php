<?php

namespace Illuminate\Validation\Rules;

use Closure;
use InvalidArgumentException;
use Stringable;

class ExcludeIf implements Stringable
{
    /**
     * The condition that validates the attribute.
     *
     * @var (\Closure(): bool)|bool
     */
    public $condition;

    /**
     * Create a new exclude validation rule based on a condition.
     *
     * @param  (\Closure(): bool)|bool  $condition
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($condition)
    {
        if ($condition instanceof Closure || is_bool($condition)) {
            $this->condition = $condition;
        } else {
            throw new InvalidArgumentException('The provided condition must be a callable or boolean.');
        }
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString()
    {
        if (is_callable($this->condition)) {
            return call_user_func($this->condition) ? 'exclude' : '';
        }

        return $this->condition ? 'exclude' : '';
    }
}
