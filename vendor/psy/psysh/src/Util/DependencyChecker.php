<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Util;

/**
 * Utility for checking PHP function availability.
 */
class DependencyChecker
{
    /**
     * Check if all functions in a list are available (exist and not disabled).
     *
     * @param string[] $functions List of function names to check
     */
    public static function functionsAvailable(array $functions): bool
    {
        foreach ($functions as $func) {
            if (!\function_exists($func)) {
                return false;
            }
        }

        return empty(self::functionsDisabled($functions));
    }

    /**
     * Check if any functions in a list are disabled.
     *
     * @param string[] $functions List of function names to check
     *
     * @return string[] List of disabled functions
     */
    public static function functionsDisabled(array $functions): array
    {
        $disabled = \array_map('strtolower', \array_map('trim', \explode(',', \ini_get('disable_functions'))));
        $disabledFunctions = \array_intersect($functions, $disabled);

        return $disabledFunctions;
    }
}
