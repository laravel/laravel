<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\String;

enum TruncateMode
{
    /**
     * Will cut exactly at given length.
     *
     * Length: 14
     * Source: Lorem ipsum dolor sit amet
     * Output: Lorem ipsum do
     */
    case Char;

    /**
     * Returns the string up to the last complete word containing the specified length.
     *
     * Length: 14
     * Source: Lorem ipsum dolor sit amet
     * Output: Lorem ipsum
     */
    case WordBefore;

    /**
     * Returns the string up to the complete word after or at the given length.
     *
     * Length: 14
     * Source: Lorem ipsum dolor sit amet
     * Output: Lorem ipsum dolor
     */
    case WordAfter;
}
