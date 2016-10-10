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

use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

/**
 * Test class for PhpSessionStorage.
 *
 * @author Drak <drak@zikula.org>
 *
 * These tests require separate processes.
 *
 * @runTestsInSeparateProcesses
 */
class PhpBridgeSessionStorageTest extends \PHPUnit_Framework_TestCase
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
     * @return PhpBridgeSessionStorage
     */
    protected function getStorage()
    {
        $storage = new PhpBridgeSessionStorage();
        $storage->registerBag(new AttributeBag());

        return $storage;
    }

    public function testPhpSession53()
    {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            $this->markTestSkipped('Test skipped, for PHP 5.3 only.');
        }

        $storage = $this->getStorage();

        $this->assertFalse(isset($_SESSION));
        $this->assertFalse($storage->getSaveHandler()->isActive());

        session_start();
        $this->assertTrue(isset($_SESSION));
        // in PHP 5.3 we cannot reliably tell if a session has started
        $this->assertFalse($storage->getSaveHandler()->isActive());
        // PHP session might have started, but the storage driver has not, so false is correct here
        $this->assertFalse($storage->isStarted());

        $key = $storage->getMetadataBag()->getStorageKey();
        $this->assertFalse(isset($_SESSION[$key]));
        $storage->start();
        $this->assertTrue(isset($_SESSION[$key]));
    }

    public function testPhpSession54()
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
        // in PHP 5.4 we can reliably detect a session started
        $this->assertTrue($storage->getSaveHandler()->isActive());
        // PHP session might have started, but the storage driver has not, so false is correct here
        $this->assertFalse($storage->isStarted());

        $key = $storage->getMetadataBag()->getStorageKey();
        $this->assertFalse(isset($_SESSION[$key]));
        $storage->start();
        $this->assertTrue(isset($_SESSION[$key]));
    }

    public function testClear()
    {
        $storage = $this->getStorage();
        session_start();
        $_SESSION['drak'] = 'loves symfony';
        $storage->getBag('attributes')->set('symfony', 'greatness');
        $key = $storage->getBag('attributes')->getStorageKey();
        $this->assertEquals($_SESSION[$key], array('symfony' => 'greatness'));
        $this->assertEquals($_SESSION['drak'], 'loves symfony');
        $storage->clear();
        $this->assertEquals($_SESSION[$key], array());
        $this->assertEquals($_SESSION['drak'], 'loves symfony');
    }
}
