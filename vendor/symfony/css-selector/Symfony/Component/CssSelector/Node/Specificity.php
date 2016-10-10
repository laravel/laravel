<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Node;

/**
 * Represents a node specificity.
 *
 * This component is a port of the Python cssselector library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @see http://www.w3.org/TR/selectors/#specificity
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class Specificity
{
    const A_FACTOR = 100;
    const B_FACTOR = 10;
    const C_FACTOR = 1;

    /**
     * @var int
     */
    private $a;

    /**
     * @var int
     */
    private $b;

    /**
     * @var int
     */
    private $c;

    /**
     * Constructor.
     *
     * @param int $a
     * @param int $b
     * @param int $c
     */
    public function __construct($a, $b, $c)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    /**
     * @param Specificity $specificity
     *
     * @return Specificity
     */
    public function plus(Specificity $specificity)
    {
        return new self($this->a + $specificity->a, $this->b + $specificity->b, $this->c + $specificity->c);
    }

    /**
     * Returns global specificity value.
     *
     * @return int
     */
    public function getValue()
    {
        return $this->a * self::A_FACTOR + $this->b * self::B_FACTOR + $this->c * self::C_FACTOR;
    }
}
