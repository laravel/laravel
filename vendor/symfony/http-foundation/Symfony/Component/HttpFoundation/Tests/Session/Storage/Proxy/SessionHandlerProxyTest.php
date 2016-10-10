<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Session\Storage\Proxy;

use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

/**
 * Tests for SessionHandlerProxy class.
 *
 * @author Drak <drak@zikula.org>
 *
 * @runTestsInSeparateProcesses
 */
class SessionHandlerProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_Matcher
     */
    private $mock;

    /**
     * @var SessionHandlerProxy
     */
    private $proxy;

    protected function setUp()
    {
        $this->mock = $this->getMock('SessionHandlerInterface');
        $this->proxy = new SessionHandlerProxy($this->mock);
    }

    protected function tearDown()
    {
        $this->mock = null;
        $this->proxy = null;
    }

    public function testOpen()
    {
        $this->mock->expects($this->once())
            ->method('open')
            ->will($this->returnValue(true));

        $this->assertFalse($this->proxy->isActive());
        $this->proxy->open('name', 'id');
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->assertTrue($this->proxy->isActive());
        } else {
            $this->assertFalse($this->proxy->isActive());
        }
    }

    public function testOpenFalse()
    {
        $this->mock->expects($this->once())
            ->method('open')
            ->will($this->returnValue(false));

        $this->assertFalse($this->proxy->isActive());
        $this->proxy->open('name', 'id');
        $this->assertFalse($this->proxy->isActive());
    }

    public function testClose()
    {
        $this->mock->expects($this->once())
            ->method('close')
            ->will($this->returnValue(true));

        $this->assertFalse($this->proxy->isActive());
        $this->proxy->close();
        $this->assertFalse($this->proxy->isActive());
    }

    public function testCloseFalse()
    {
        $this->mock->expects($this->once())
            ->method('close')
            ->will($this->returnValue(false));

        $this->assertFalse($this->proxy->isActive());
        $this->proxy->close();
        $this->assertFalse($this->proxy->isActive());
    }

    public function testRead()
    {
        $this->mock->expects($this->once())
            ->method('read');

        $this->proxy->read('id');
    }

    public function testWrite()
    {
        $this->mock->expects($this->once())
            ->method('write');

        $this->proxy->write('id', 'data');
    }

    public function testDestroy()
    {
        $this->mock->expects($this->once())
            ->method('destroy');

        $this->proxy->destroy('id');
    }

    public function testGc()
    {
        $this->mock->expects($this->once())
            ->method('gc');

        $this->proxy->gc(86400);
    }
}
