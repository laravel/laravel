<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Tests;

use Symfony\Component\Debug\ExceptionHandler;

class MockExceptionHandler extends Exceptionhandler
{
    public $e;

    public function handle(\Exception $e)
    {
        $this->e = $e;
    }
}
