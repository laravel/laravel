<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Session\Storage\Handler;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;

class MemcachedSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    const PREFIX = 'prefix_';
    const TTL = 1000;

    /**
     * @var MemcachedSessionHandler
     */
    protected $storage;

    protected $memcached;

    protected function setUp()
    {
        if (!class_exists('Memcached')) {
            $this->markTestSkipped('Skipped tests Memcached class is not present');
        }

        $this->memcached = $this->getMock('Memcached');
        $this->storage = new MemcachedSessionHandler(
            $this->memcached,
            array('prefix' => self::PREFIX, 'expiretime' => self::TTL)
        );
    }

    protected function tearDown()
    {
        $this->memcached = null;
        $this->storage = null;
    }

    public function testOpenSession()
    {
        $this->assertTrue($this->storage->open('', ''));
    }

    public function testCloseSession()
    {
        $this->assertTrue($this->storage->close());
    }

    public function testReadSession()
    {
        $this->memcached
            ->expects($this->once())
            ->method('get')
            ->with(self::PREFIX.'id')
        ;

        $this->assertEquals('', $this->storage->read('id'));
    }

    public function testWriteSession()
    {
        $this->memcached
            ->expects($this->once())
            ->method('set')
            ->with(self::PREFIX.'id', 'data', $this->equalTo(time() + self::TTL, 2))
            ->will($this->returnValue(true))
        ;

        $this->assertTrue($this->storage->write('id', 'data'));
    }

    public function testDestroySession()
    {
        $this->memcached
            ->expects($this->once())
            ->method('delete')
            ->with(self::PREFIX.'id')
            ->will($this->returnValue(true))
        ;

        $this->assertTrue($this->storage->destroy('id'));
    }

    public function testGcSession()
    {
        $this->assertTrue($this->storage->gc(123));
    }

    /**
     * @dataProvider getOptionFixtures
     */
    public function testSupportedOptions($options, $supported)
    {
        try {
            new MemcachedSessionHandler($this->memcached, $options);
            $this->assertTrue($supported);
        } catch (\InvalidArgumentException $e) {
            $this->assertFalse($supported);
        }
    }

    public function getOptionFixtures()
    {
        return array(
            array(array('prefix' => 'session'), true),
            array(array('expiretime' => 100), true),
            array(array('prefix' => 'session', 'expiretime' => 200), true),
            array(array('expiretime' => 100, 'foo' => 'bar'), false),
        );
    }

    public function testGetConnection()
    {
        $method = new \ReflectionMethod($this->storage, 'getMemcached');
        $method->setAccessible(true);

        $this->assertInstanceOf('\Memcached', $method->invoke($this->storage));
    }
}
