<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\Profiler;

use Symfony\Component\HttpKernel\Profiler\MemcacheProfilerStorage;
use Symfony\Component\HttpKernel\Tests\Profiler\Mock\MemcacheMock;

class MemcacheProfilerStorageTest extends AbstractProfilerStorageTest
{
    protected static $storage;

    protected function setUp()
    {
        $memcacheMock = new MemcacheMock();
        $memcacheMock->addServer('127.0.0.1', 11211);

        self::$storage = new MemcacheProfilerStorage('memcache://127.0.0.1:11211', '', '', 86400);
        self::$storage->setMemcache($memcacheMock);

        if (self::$storage) {
            self::$storage->purge();
        }
    }

    protected function tearDown()
    {
        if (self::$storage) {
            self::$storage->purge();
            self::$storage = false;
        }
    }

    /**
     * @return \Symfony\Component\HttpKernel\Profiler\ProfilerStorageInterface
     */
    protected function getStorage()
    {
        return self::$storage;
    }
}
