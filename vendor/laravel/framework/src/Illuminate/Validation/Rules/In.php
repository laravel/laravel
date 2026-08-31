<?php

namespace Illuminate\Validation\Rules;

use Illuminate\Contracts\Support\Arrayable;
use Stringable;

use function Illuminate\Support\enum_value;

class In implements Stringable
{
    /**
     * The name of the rule.
     *
     * @var string
     */
    protected $rule = 'in';

    /**
     * The accepted values.
     *
     * @var array
     */
    protected $values;

    /**
     * Create a new in rule instance.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|\UnitEnum|array|string  $values
     */
    public function __construct($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        $this->values = is_array($values) ? $values : func_get_args();
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     *
     * @see \Illuminate\Validation\ValidationRuleParser::parseParameters
     */
    public function __toString()
    {
        $values = array_map(function ($value) {
            $value = enum_value($value);

            return '"'.str_replace('"', '""', $value).'"';
        }, $this->values);

        return $this->rule.':'.implode(',', $values);
    }
}
