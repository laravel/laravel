<?php
namespace Hamcrest\Type;

/*
 Copyright (c) 2010 hamcrest.org
 */
use Hamcrest\Core\IsTypeOf;

/**
 * Tests whether the value is numeric.
 */
class IsNumeric extends IsTypeOf
{

    public function __construct()
    {
        parent::__construct('number');
    }

    public function matches($item)
    {
        if ($this->isHexadecimal($item)) {
            return true;
        }

        return is_numeric($item);
    }

    /**
     * Return if the string passed is a valid hexadecimal number.
     * This check is necessary because PHP 7 doesn't recognize hexadecimal string as numeric anymore.
     *
     * @param mixed $item
     * @return boolean
     */
    private function isHexadecimal($item)
    {
        if (is_string($item) && preg_match('/^0x(.*)$/', $item, $matches)) {
            return ctype_xdigit($matches[1]);
        }

        return false;
    }

    /**
     * Is the value a numeric?
     *
     * @factory
     */
    public static function numericValue()
    {
        return new self;
    }
}
