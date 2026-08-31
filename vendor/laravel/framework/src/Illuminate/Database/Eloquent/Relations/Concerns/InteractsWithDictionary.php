<?php

namespace Illuminate\Database\Eloquent\Relations\Concerns;

use InvalidArgumentException;
use UnitEnum;

use function Illuminate\Support\enum_value;

trait InteractsWithDictionary
{
    /**
     * Get a dictionary key attribute - casting it to a string if necessary.
     *
     * @param  mixed  $attribute
     * @return string|int|null
     *
     * @throws \InvalidArgumentException
     */
    protected function getDictionaryKey($attribute)
    {
        if (is_null($attribute) || is_string($attribute) || is_int($attribute)) {
            return $attribute;
        }

        if (is_object($attribute)) {
            if (method_exists($attribute, '__toString')) {
                return $attribute->__toString();
            }

            if ($attribute instanceof UnitEnum) {
                return enum_value($attribute);
            }

            throw new InvalidArgumentException('Model attribute value is an object but does not have a __toString method.');
        }

        return (string) $attribute;
    }
}
