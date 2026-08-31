<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\TabCompletion\AutoloadWarmer;

/**
 * Autoload warmer for tab completion.
 *
 * Autoload warmers load classes from autoloaders at startup to enable
 * better tab completion. This leverages PHP's existing autoload system
 * rather than maintaining a separate list of available classes.
 */
interface AutoloadWarmerInterface
{
    /**
     * Warm up the autoloader by loading classes.
     *
     * This uses PHP's autoload system (class_exists, etc.) to actually load
     * classes, making them available for tab completion via get_declared_classes().
     *
     * @return int Number of classes/interfaces/traits that were loaded
     */
    public function warm(): int;
}
