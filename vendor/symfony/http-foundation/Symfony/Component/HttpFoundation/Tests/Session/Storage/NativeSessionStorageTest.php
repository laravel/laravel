<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\Session\Storage;

use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

/**
 * Test class for NativeSessionStorage.
 *
 * @author Drak <drak@zikula.org>
 *
 * These tests require separate processes.
 *
 * @runTestsInSeparateProcesses
 */
class NativeSessionStorageTest extends \PHPUnit_Framework_TestCase
{
    private $savePath;

    protected function setUp()
    {
        ini_set('session.save_handler', 'files');
        ini_set('session.save_path', $this->savePath = sys_get_temp_dir().'/sf2test');
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath);
        }
    }

    protected function tearDown()
    {
        session_write_close();
        array_map('unlink', glob($this->savePath.'/*'));
        if (is_dir($this->savePath)) {
            rmdir($this->savePath);
        }

        $this->savePath = null;
    }

    /**
     * @param array $options
     *
     * @return NativeSessionStorage
     */
    protected function getStorage(array $options = array())
    {
        $storage = new NativeSessionStorage($options);
        $storage->registerBag(new AttributeBag());

        return $storage;
    }

    public function testBag()
    {
        $storage = $this->getStorage();
        $bag = new FlashBag();
        $storage->registerBag($bag);
        $this->assertSame($bag, $storage->getBag($bag->getName()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRegisterBagException()
    {
        $storage = $this->getStorage();
        $storage->getBag('non_existing');
    }

    public function testGetId()
    {
        $storage = $this->getStorage();
        $this->assertEquals('', $storage->getId());

        $storage->start();
        $this->assertNotEquals('', $storage->getId());

        $storage->save();
        $this->assertNotEquals('', $storage->getId());
    }

    public function testRegenerate()
    {
        $storage = $this->getStorage();
        $storage->start();
        $id = $storage->getId();
        $storage->getBag('attributes')->set('lucky', 7);
        $storage->regenerate();
        $this->assertNotEquals($id, $storage->getId());
        $this->assertEquals(7, $storage->getBag('attributes')->get('lucky'));
    }

    public function testRegenerateDestroy()
    {
        $storage = $this->getStorage();
        $storage->start();
        $id = $storage->getId();
        $storage->getBag('attributes')->set('legs', 11);
        $storage->regenerate(true);
        $this->assertNotEquals($id, $storage->getId());
        $this->assertEquals(11, $storage->getBag('attributes')->get('legs'));
    }

    public function testDefaultSessionCacheLimiter()
    {
        ini_set('session.cache_limiter', 'nocache');

        $storage = new NativeSessionStorage();
        $this->assertEquals('', ini_get('session.cache_limiter'));
    }

    public function testExplicitSessionCacheLimiter()
    {
        ini_set('session.cache_limiter', 'nocache');

        $storage = new NativeSessionStorage(array('cache_limiter' => 'public'));
        $this->assertEquals('public', ini_get('session.cache_limiter'));
    }

    public function testCookieOptions()
    {
        $options = array(
            'cookie_lifetime' => 123456,
            'cookie_path' => '/my/cookie/path',
            'cookie_domain' => 'symfony2.example.com',
            'cookie_secure' => true,
            'cookie_httponly' => false,
        );

        $this->getStorage($options);
        $temp = session_get_cookie_params();
        $gco = array();

        foreach ($temp as $key => $value) {
            $gco['cookie_'.$key] = $value;
        }

        $this->assertEquals($options, $gco);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetSaveHandlerException()
    {
        $storage = $this->getStorage();
        $storage->setSaveHandler(new \stdClass());
    }

    public function testSetSaveHandler53()
    {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $this->markTestSkipped('Test skipped, for PHP 5.3 only.');
        }

        ini_set('session.save_handler', 'files');
        $storage = $this->getStorage();
        $storage->setSaveHandler();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy', $storage->getSaveHandler());
        $storage->setSaveHandler(null);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy', $storage->getSaveHandler());
        $storage->setSaveHandler(new NativeSessionHandler());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy', $storage->getSaveHandler());
        $storage->setSaveHandler(new SessionHandlerProxy(new NullSessionHandler()));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy', $storage->getSaveHandler());
        $storage->setSaveHandler(new NullSessionHandler());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy', $storage->getSaveHandler());
        $storage->setSaveHandler(new NativeProxy());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy', $storage->getSaveHandler());
    }

    public function testSetSaveHandler54()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->markTestSkipped('Test skipped, for PHP 5.4 only.');
        }

        ini_set('session.save_handler', 'files');
        $storage = $this->getStorage();
        $storage->setSaveHandler();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy', $storage->getSaveHandler());
        $storage->setSaveHandler(null);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy', $storage->getSaveHandler());
        $storage->setSaveHandler(new SessionHandlerProxy(new NativeSessionHandler()));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy', $storage->getSaveHandler());
        $storage->setSaveHandler(new NativeSessionHandler());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy', $storage->getSaveHandler());
        $storage->setSaveHandler(new SessionHandlerProxy(new NullSessionHandler()));
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy', $storage->getSaveHandler());
        $storage->setSaveHandler(new NullSessionHandler());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy', $storage->getSaveHandler());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testStartedOutside53()
    {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $this->markTestSkipped('Test skipped, for PHP 5.3 only.');
        }

        $storage = $this->getStorage();

        $this->assertFalse(isset($_SESSION));

        session_start();
        $this->assertTrue(isset($_SESSION));
        // PHP session might have started, but the storage driver has not, so false is correct here
        $this->assertFalse($storage->isStarted());

        $key = $storage->getMetadataBag()->getStorageKey();
        $this->assertFalse(isset($_SESSION[$key]));
        $storage->start();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCanStartOutside54()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->markTestSkipped('Test skipped, for PHP 5.4 only.');
        }

        $storage = $this->getStorage();

        $this->assertFalse(isset($_SESSION));
        $this->assertFalse($storage->getSaveHandler()->isActive());
        $this->assertFalse($storage->isStarted());

        session_start();
        $this->assertTrue(isset($_SESSION));
        $this->assertTrue($storage->getSaveHandler()->isActive());
        // PHP session might have started, but the storage driver has not, so false is correct here
        $this->assertFalse($storage->isStarted());

        $key = $storage->getMetadataBag()->getStorageKey();
        $this->assertFalse(isset($_SESSION[$key]));
        $storage->start();
    }
}
