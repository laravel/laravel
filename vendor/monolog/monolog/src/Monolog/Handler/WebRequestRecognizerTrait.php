<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

trait WebRequestRecognizerTrait
{
    /**
     * Checks if PHP's serving a web request
     */
    protected function isWebRequest(): bool
    {
        return 'cli' !== \PHP_SAPI && 'phpdbg' !== \PHP_SAPI;
    }
}
