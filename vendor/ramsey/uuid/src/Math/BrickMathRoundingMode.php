<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Math;

use Brick\Math\RoundingMode as BrickMathRounding;

/**
 * @internal Polyfill for Brick\Math\RoundingMode constant naming
 * changes introduced in brick/math 0.15 (UPPER_SNAKE_CASE → PascalCase)
 */
final class BrickMathRoundingMode
{
    /**
     * Maps ramsey/uuid rounding mode constants to their PascalCase (>= 0.15)
     * and UPPER_SNAKE_CASE (< 0.15) equivalents in brick/math
     */
    private const ROUNDING_MODE_MAP = [
        RoundingMode::UNNECESSARY => ['Unnecessary', 'UNNECESSARY'],
        RoundingMode::UP => ['Up', 'UP'],
        RoundingMode::DOWN => ['Down', 'DOWN'],
        RoundingMode::CEILING => ['Ceiling', 'CEILING'],
        RoundingMode::FLOOR => ['Floor', 'FLOOR'],
        RoundingMode::HALF_UP => ['HalfUp', 'HALF_UP'],
        RoundingMode::HALF_DOWN => ['HalfDown', 'HALF_DOWN'],
        RoundingMode::HALF_CEILING => ['HalfCeiling', 'HALF_CEILING'],
        RoundingMode::HALF_FLOOR => ['HalfFloor', 'HALF_FLOOR'],
        RoundingMode::HALF_EVEN => ['HalfEven', 'HALF_EVEN'],
    ];

    /**
     * Resolves a ramsey/uuid rounding mode to the correct
     * Brick\Math\RoundingMode value for the installed version.
     *
     * @return BrickMathRounding::*
     */
    public static function resolve(int $roundingMode)
    {
        [$pascal, $snake] = self::ROUNDING_MODE_MAP[$roundingMode]
            ?? self::ROUNDING_MODE_MAP[RoundingMode::UNNECESSARY];

        $class = BrickMathRounding::class;

        /** @var BrickMathRounding::* */
        return defined("$class::$pascal")
            ? constant("$class::$pascal")
            : constant("$class::$snake");
    }
}
