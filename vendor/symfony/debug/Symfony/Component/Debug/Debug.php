<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug;

/**
 * Registers all the debug tools.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Debug
{
    private static $enabled = false;

    /**
     * Enables the debug tools.
     *
     * This method registers an error handler and an exception handler.
     *
     * If the Symfony ClassLoader component is available, a special
     * class loader is also registered.
     *
     * @param int     $errorReportingLevel The level of error reporting you want
     * @param bool    $displayErrors       Whether to display errors (for development) or just log them (for production)
     */
    public static function enable($errorReportingLevel = null, $displayErrors = true)
    {
        if (static::$enabled) {
            return;
        }

        static::$enabled = true;

        error_reporting(-1);

        ErrorHandler::register($errorReportingLevel, $displayErrors);
        if ('cli' !== php_sapi_name()) {
            ExceptionHandler::register();
        // CLI - display errors only if they're not already logged to STDERR
        } elseif ($displayErrors && (!ini_get('log_errors') || ini_get('error_log'))) {
            ini_set('display_errors', 1);
        }

        DebugClassLoader::enable();
    }
}
