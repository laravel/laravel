<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Matcher;

use function array_replace_recursive;
use function implode;
use function is_array;

class Subset extends MatcherAbstract
{
    private $expected;

    private $strict = true;

    /**
     * @param array $expected Expected subset of data
     * @param bool  $strict   Whether to run a strict or loose comparison
     */
    public function __construct(array $expected, $strict = true)
    {
        $this->expected = $expected;
        $this->strict = $strict;
    }

    /**
     * Return a string representation of this Matcher
     *
     * @return string
     */
    public function __toString()
    {
        return '<Subset' . $this->formatArray($this->expected) . '>';
    }

    /**
     * @param array $expected Expected subset of data
     *
     * @return Subset
     */
    public static function loose(array $expected)
    {
        return new static($expected, false);
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
        if (! is_array($actual)) {
            return false;
        }

        if ($this->strict) {
            return $actual === array_replace_recursive($actual, $this->expected);
        }

        return $actual == array_replace_recursive($actual, $this->expected);
    }

    /**
     * @param array $expected Expected subset of data
     *
     * @return Subset
     */
    public static function strict(array $expected)
    {
        return new static($expected, true);
    }

    /**
     * Recursively format an array into the string representation for this matcher
     *
     * @return string
     */
    protected function formatArray(array $array)
    {
        $elements = [];
        foreach ($array as $k => $v) {
            $elements[] = $k . '=' . (is_array($v) ? $this->formatArray($v) : (string) $v);
        }

        return '[' . implode(', ', $elements) . ']';
    }
}
