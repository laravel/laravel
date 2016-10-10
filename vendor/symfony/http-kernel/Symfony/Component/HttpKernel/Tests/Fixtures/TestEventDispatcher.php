<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Fixtures;

use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class TestEventDispatcher extends EventDispatcher implements TraceableEventDispatcherInterface
{
    public function getCalledListeners()
    {
        return array('foo');
    }

    public function getNotCalledListeners()
    {
        return array('bar');
    }
}
