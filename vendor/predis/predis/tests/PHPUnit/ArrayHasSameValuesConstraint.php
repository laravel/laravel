<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Constraint that accepts arrays with the same elements but different order.
 */
class ArrayHasSameValuesConstraint extends PHPUnit_Framework_Constraint
{
    protected $array;

    /**
     * @param array $array
     */
    public function __construct($array)
    {
        $this->array = $array;
    }

    /**
     * {@inheritdoc}
     */
    public function matches($other)
    {
        if (count($this->array) !== count($other)) {
            return false;
        }

        if (array_diff($this->array, $other)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return 'two arrays contain the same elements.';
    }

    /**
     * {@inheritdoc}
     */
    protected function failureDescription($other)
    {
        return $this->toString();
    }
}
