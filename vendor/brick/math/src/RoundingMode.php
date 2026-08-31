<?php

declare(strict_types=1);

namespace Brick\Math;

/**
 * Specifies rounding behavior by defining how discarded digits affect the returned result when an exact value cannot
 * be represented at the requested scale.
 */
enum RoundingMode
{
    /**
     * Asserts that the requested operation has an exact result, hence no rounding is necessary.
     *
     * If this rounding mode is specified on an operation that yields a result that
     * cannot be represented at the requested scale, a RoundingNecessaryException is thrown.
     */
    case Unnecessary;

    /**
     * Rounds away from zero.
     *
     * Always increments the digit prior to a nonzero discarded fraction.
     * Note that this rounding mode never decreases the magnitude of the calculated value.
     */
    case Up;

    /**
     * Rounds towards zero.
     *
     * Never increments the digit prior to a discarded fraction (i.e., truncates).
     * Note that this rounding mode never increases the magnitude of the calculated value.
     */
    case Down;

    /**
     * Rounds towards positive infinity.
     *
     * If the result is positive, behaves as for Up; if negative, behaves as for Down.
     * Note that this rounding mode never decreases the calculated value.
     */
    case Ceiling;

    /**
     * Rounds towards negative infinity.
     *
     * If the result is positive, behaves as for Down; if negative, behaves as for Up.
     * Note that this rounding mode never increases the calculated value.
     */
    case Floor;

    /**
     * Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round up.
     *
     * Behaves as for Up if the discarded fraction is >= 0.5; otherwise, behaves as for Down.
     * Note that this is the rounding mode commonly taught at school.
     */
    case HalfUp;

    /**
     * Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round down.
     *
     * Behaves as for Up if the discarded fraction is > 0.5; otherwise, behaves as for Down.
     */
    case HalfDown;

    /**
     * Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round towards positive infinity.
     *
     * If the result is positive, behaves as for HalfUp; if negative, behaves as for HalfDown.
     */
    case HalfCeiling;

    /**
     * Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round towards negative infinity.
     *
     * If the result is positive, behaves as for HalfDown; if negative, behaves as for HalfUp.
     */
    case HalfFloor;

    /**
     * Rounds towards the "nearest neighbor" unless both neighbors are equidistant, in which case rounds towards the even neighbor.
     *
     * Behaves as for HalfUp if the digit to the left of the discarded fraction is odd;
     * behaves as for HalfDown if it's even.
     *
     * Note that this is the rounding mode that statistically minimizes
     * cumulative error when applied repeatedly over a sequence of calculations.
     * It is sometimes known as "Banker's rounding", and is chiefly used in the USA.
     */
    case HalfEven;

    /**
     * @deprecated Use RoundingMode::Unnecessary instead.
     */
    public const UNNECESSARY = self::Unnecessary;

    /**
     * @deprecated Use RoundingMode::Up instead.
     */
    public const UP = self::Up;

    /**
     * @deprecated Use RoundingMode::Down instead.
     */
    public const DOWN = self::Down;

    /**
     * @deprecated Use RoundingMode::Ceiling instead.
     */
    public const CEILING = self::Ceiling;

    /**
     * @deprecated Use RoundingMode::Floor instead.
     */
    public const FLOOR = self::Floor;

    /**
     * @deprecated Use RoundingMode::HalfUp instead.
     */
    public const HALF_UP = self::HalfUp;

    /**
     * @deprecated Use RoundingMode::HalfDown instead.
     */
    public const HALF_DOWN = self::HalfDown;

    /**
     * @deprecated Use RoundingMode::HalfCeiling instead.
     */
    public const HALF_CEILING = self::HalfCeiling;

    /**
     * @deprecated Use RoundingMode::HalfFloor instead.
     */
    public const HALF_FLOOR = self::HalfFloor;

    /**
     * @deprecated Use RoundingMode::HalfEven instead.
     */
    public const HALF_EVEN = self::HalfEven;
}
