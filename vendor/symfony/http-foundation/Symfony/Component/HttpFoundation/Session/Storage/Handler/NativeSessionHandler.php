<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * Adds SessionHandler functionality if available.
 *
 * @see http://php.net/sessionhandler
 */

if (version_compare(phpversion(), '5.4.0', '>=')) {
    class NativeSessionHandler extends \SessionHandler
    {
    }
} else {
    class NativeSessionHandler
    {
    }
}
