<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (\PHP_VERSION_ID < 80400) {
    enum RoundingMode
    {
        case HalfAwayFromZero;
        case HalfTowardsZero;
        case HalfEven;
        case HalfOdd;
        case TowardsZero;
        case AwayFromZero;
        case NegativeInfinity;
        case PositiveInfinity;
    }
}
