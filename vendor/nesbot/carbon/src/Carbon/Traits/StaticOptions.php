<?php

declare(strict_types=1);

/**
 * This file is part of the Carbon package.
 *
 * (c) Brian Nesbitt <brian@nesbot.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carbon\Traits;

use Carbon\FactoryImmutable;

/**
 * Options related to a static variable.
 */
trait StaticOptions
{
    ///////////////////////////////////////////////////////////////////
    ///////////// Behavior customization for sub-classes //////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Function to call instead of format.
     *
     * @var string|callable|null
     */
    protected static $formatFunction;

    /**
     * Function to call instead of createFromFormat.
     *
     * @var string|callable|null
     */
    protected static $createFromFormatFunction;

    /**
     * Function to call instead of parse.
     *
     * @var string|callable|null
     */
    protected static $parseFunction;

    ///////////////////////////////////////////////////////////////////
    ///////////// Use default factory for static options //////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * @deprecated To avoid conflict between different third-party libraries, static setters should not be used.
     *             You should rather use the ->settings() method.
     * @see settings
     *
     * Enable the strict mode (or disable with passing false).
     *
     * @param bool $strictModeEnabled
     */
    public static function useStrictMode(bool $strictModeEnabled = true): void
    {
        FactoryImmutable::getDefaultInstance()->useStrictMode($strictModeEnabled);
    }

    /**
     * Returns true if the strict mode is globally in use, false else.
     * (It can be overridden in specific instances.)
     *
     * @return bool
     */
    public static function isStrictModeEnabled(): bool
    {
        return FactoryImmutable::getInstance()->isStrictModeEnabled();
    }

    /**
     * @deprecated To avoid conflict between different third-party libraries, static setters should not be used.
     *             You should rather use the ->settings() method.
     *             Or you can use method variants: addMonthsWithOverflow/addMonthsNoOverflow, same variants
     *             are available for quarters, years, decade, centuries, millennia (singular and plural forms).
     * @see settings
     *
     * Indicates if months should be calculated with overflow.
     *
     * @param bool $monthsOverflow
     *
     * @return void
     */
    public static function useMonthsOverflow(bool $monthsOverflow = true): void
    {
        FactoryImmutable::getDefaultInstance()->useMonthsOverflow($monthsOverflow);
    }

    /**
     * @deprecated To avoid conflict between different third-party libraries, static setters should not be used.
     *             You should rather use the ->settings() method.
     *             Or you can use method variants: addMonthsWithOverflow/addMonthsNoOverflow, same variants
     *             are available for quarters, years, decade, centuries, millennia (singular and plural forms).
     * @see settings
     *
     * Reset the month overflow behavior.
     *
     * @return void
     */
    public static function resetMonthsOverflow(): void
    {
        FactoryImmutable::getDefaultInstance()->resetMonthsOverflow();
    }

    /**
     * Get the month overflow global behavior (can be overridden in specific instances).
     *
     * @return bool
     */
    public static function shouldOverflowMonths(): bool
    {
        return FactoryImmutable::getInstance()->shouldOverflowMonths();
    }

    /**
     * @deprecated To avoid conflict between different third-party libraries, static setters should not be used.
     *             You should rather use the ->settings() method.
     *             Or you can use method variants: addYearsWithOverflow/addYearsNoOverflow, same variants
     *             are available for quarters, years, decade, centuries, millennia (singular and plural forms).
     * @see settings
     *
     * Indicates if years should be calculated with overflow.
     *
     * @param bool $yearsOverflow
     *
     * @return void
     */
    public static function useYearsOverflow(bool $yearsOverflow = true): void
    {
        FactoryImmutable::getDefaultInstance()->useYearsOverflow($yearsOverflow);
    }

    /**
     * @deprecated To avoid conflict between different third-party libraries, static setters should not be used.
     *             You should rather use the ->settings() method.
     *             Or you can use method variants: addYearsWithOverflow/addYearsNoOverflow, same variants
     *             are available for quarters, years, decade, centuries, millennia (singular and plural forms).
     * @see settings
     *
     * Reset the month overflow behavior.
     *
     * @return void
     */
    public static function resetYearsOverflow(): void
    {
        FactoryImmutable::getDefaultInstance()->resetYearsOverflow();
    }

    /**
     * Get the month overflow global behavior (can be overridden in specific instances).
     *
     * @return bool
     */
    public static function shouldOverflowYears(): bool
    {
        return FactoryImmutable::getInstance()->shouldOverflowYears();
    }
}
