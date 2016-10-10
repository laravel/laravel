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

use Symfony\Component\HttpFoundation\Session\Storage\Proxy\AbstractProxy;

// Note until PHPUnit_Mock_Objects 1.2 is released you cannot mock abstracts due to
// https://github.com/sebastianbergmann/phpunit-mock-objects/issues/73
class ConcreteProxy extends AbstractProxy
{
}

class ConcreteSessionHandlerInterfaceProxy extends AbstractProxy implements \SessionHandlerInterface
{
    public function open($savePath, $sessionName)
    {
    }

    public function close()
    {
    }

    public function read($id)
    {
    }

    public function write($id, $data)
    {
    }

    public function destroy($id)
    {
    }

    public function gc($maxlifetime)
    {
    }
}

/**
 * Test class for AbstractProxy.
 *
 * @author Drak <drak@zikula.org>
 */
class AbstractProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractProxy
     */
    protected $proxy;

    protected function setUp()
    {
        $this->proxy = new ConcreteProxy();
    }

    protected function tearDown()
    {
        $this->proxy = null;
    }

    public function testGetSaveHandlerName()
    {
        $this->assertNull($this->proxy->getSaveHandlerName());
    }

    public function testIsSessionHandlerInterface()
    {
        $this->assertFalse($this->proxy->isSessionHandlerInterface());
        $sh = new ConcreteSessionHandlerInterfaceProxy();
        $this->assertTrue($sh->isSessionHandlerInterface());
    }

    public function testIsWrapper()
    {
        $this->assertFalse($this->proxy->isWrapper());
    }

    public function testIsActivePhp53()
    {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $this->markTestSkipped('Test skipped, for PHP 5.3 only.');
        }

        $this->assertFalse($this->proxy->isActive());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIsActivePhp54()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->markTestSkipped('Test skipped, for PHP 5.4 only.');
        }

        $this->assertFalse($this->proxy->isActive());
        session_start();
        $this->assertTrue($this->proxy->isActive());
    }

    public function testSetActivePhp53()
    {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $this->markTestSkipped('Test skipped, for PHP 5.3 only.');
        }

        $this->proxy->setActive(true);
        $this->assertTrue($this->proxy->isActive());
        $this->proxy->setActive(false);
        $this->assertFalse($this->proxy->isActive());
    }

    /**
     * @runInSeparateProcess
     * @expectedException \LogicException
     */
    public function testSetActivePhp54()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->markTestSkipped('Test skipped, for PHP 5.4 only.');
        }

        $this->proxy->setActive(true);
    }

    /**
     * @runInSeparateProcess
     */
    public function testName()
    {
        $this->assertEquals(session_name(), $this->proxy->getName());
        $this->proxy->setName('foo');
        $this->assertEquals('foo', $this->proxy->getName());
        $this->assertEquals(session_name(), $this->proxy->getName());
    }

    /**
     * @expectedException \LogicException
     */
    public function testNameExceptionPhp53()
    {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $this->markTestSkipped('Test skipped, for PHP 5.3 only.');
        }

        $this->proxy->setActive(true);
        $this->proxy->setName('foo');
    }

    /**
     * @runInSeparateProcess
     * @expectedException \LogicException
     */
    public function testNameExceptionPhp54()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->markTestSkipped('Test skipped, for PHP 5.4 only.');
        }

        session_start();
        $this->proxy->setName('foo');
    }

    /**
     * @runInSeparateProcess
     */
    public function testId()
    {
        $this->assertEquals(session_id(), $this->proxy->getId());
        $this->proxy->setId('foo');
        $this->assertEquals('foo', $this->proxy->getId());
        $this->assertEquals(session_id(), $this->proxy->getId());
    }

    /**
     * @expectedException \LogicException
     */
    public function testIdExceptionPhp53()
    {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $this->markTestSkipped('Test skipped, for PHP 5.3 only.');
        }

        $this->proxy->setActive(true);
        $this->proxy->setId('foo');
    }

    /**
     * @runInSeparateProcess
     * @expectedException \LogicException
     */
    public function testIdExceptionPhp54()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->markTestSkipped('Test skipped, for PHP 5.4 only.');
        }

        session_start();
        $this->proxy->setId('foo');
    }
}
