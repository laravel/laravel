<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Debug;

use Symfony\Component\Debug\ExceptionHandler as DebugExceptionHandler;

/**
 * ExceptionHandler converts an exception to a Response object.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @deprecated Deprecated in 2.3, to be removed in 3.0. Use the same class from the Debug component instead.
 */
class ExceptionHandler extends DebugExceptionHandler
{
}
