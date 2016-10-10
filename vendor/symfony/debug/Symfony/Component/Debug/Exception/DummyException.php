<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Exception;

/**
 * Used to stop execution of a PHP script after handling a fatal error.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DummyException extends \ErrorException
{
}
