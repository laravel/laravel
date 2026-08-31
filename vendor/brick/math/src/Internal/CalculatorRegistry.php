<?php

declare(strict_types=1);

namespace Brick\Math\Internal;

use function extension_loaded;

/**
 * Stores the current Calculator instance used by BigNumber classes.
 *
 * @internal
 */
final class CalculatorRegistry
{
    /**
     * The Calculator instance in use.
     */
    private static ?Calculator $instance = null;

    /**
     * Sets the Calculator instance to use.
     *
     * An instance is typically set only in unit tests: autodetect is usually the best option.
     *
     * @param Calculator|null $calculator The calculator instance, or null to revert to autodetect.
     */
    final public static function set(?Calculator $calculator): void
    {
        self::$instance = $calculator;
    }

    /**
     * Returns the Calculator instance to use.
     *
     * If none has been explicitly set, the fastest available implementation will be returned.
     *
     * Note: even though this method is not technically pure, it is considered pure when used in a normal context, when
     * only relying on autodetect.
     *
     * @pure
     */
    final public static function get(): Calculator
    {
        /** @phpstan-ignore impure.staticPropertyAccess */
        if (self::$instance === null) {
            /** @phpstan-ignore impure.propertyAssign */
            self::$instance = self::detect();
        }

        /** @phpstan-ignore impure.staticPropertyAccess */
        return self::$instance;
    }

    /**
     * Returns the fastest available Calculator implementation.
     *
     * @pure
     *
     * @codeCoverageIgnore
     */
    private static function detect(): Calculator
    {
        if (extension_loaded('gmp')) {
            return new Calculator\GmpCalculator();
        }

        if (extension_loaded('bcmath')) {
            return new Calculator\BcMathCalculator();
        }

        return new Calculator\NativeCalculator();
    }
}
