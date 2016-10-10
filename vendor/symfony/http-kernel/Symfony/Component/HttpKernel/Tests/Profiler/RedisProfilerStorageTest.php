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

use Symfony\Component\HttpKernel\Profiler\RedisProfilerStorage;
use Symfony\Component\HttpKernel\Tests\Profiler\Mock\RedisMock;

class RedisProfilerStorageTest extends AbstractProfilerStorageTest
{
    protected static $storage;

    protected function setUp()
    {
        $redisMock = new RedisMock();
        $redisMock->connect('127.0.0.1', 6379);

        self::$storage = new RedisProfilerStorage('redis://127.0.0.1:6379', '', '', 86400);
        self::$storage->setRedis($redisMock);

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
