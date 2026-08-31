<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

use function array_values;
use function implode;

class Contains extends MatcherAbstract
{
    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    public function __toString()
    {
        $elements = [];
        foreach ($this->_expected as $v) {
            $elements[] = (string) $v;
        }

        return '<Contains[' . implode(', ', $elements) . ']>';
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
        $values = array_values($actual);
        foreach ($this->_expected as $exp) {
            $match = false;
            foreach ($values as $val) {
                if ($exp === $val || $exp == $val) {
                    $match = true;
                    break;
                }
            }

            if ($match === false) {
                return false;
            }
        }

        return true;
    }
}
