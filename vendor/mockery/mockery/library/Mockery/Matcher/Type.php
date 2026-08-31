<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

use function class_exists;
use function function_exists;
use function interface_exists;
use function is_string;
use function strtolower;
use function ucfirst;

class Type extends MatcherAbstract
{
    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    public function __toString()
    {
        return '<' . ucfirst($this->_expected) . '>';
    }

    /**
     * Check if the actual value matches the expected.
     *
     * @template TMixed
     *
     * @param TMixed $actual
     *
     * @return bool
     */
    public function match(&$actual)
    {
        $function = $this->_expected === 'real' ? 'is_float' : 'is_' . strtolower($this->_expected);

        if (function_exists($function)) {
            return $function($actual);
        }

        if (! is_string($this->_expected)) {
            return false;
        }

        if (class_exists($this->_expected) || interface_exists($this->_expected)) {
            return $actual instanceof $this->_expected;
        }

        return false;
    }
}
