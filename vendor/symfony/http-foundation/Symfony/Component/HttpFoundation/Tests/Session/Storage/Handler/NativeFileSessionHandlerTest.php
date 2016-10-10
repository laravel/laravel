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

use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

/**
 * Test class for NativeFileSessionHandler.
 *
 * @author Drak <drak@zikula.org>
 *
 * @runTestsInSeparateProcesses
 */
class NativeFileSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $storage = new NativeSessionStorage(array('name' => 'TESTING'), new NativeFileSessionHandler(sys_get_temp_dir()));

        if (version_compare(phpversion(), '5.4.0', '<')) {
            $this->assertEquals('files', $storage->getSaveHandler()->getSaveHandlerName());
            $this->assertEquals('files', ini_get('session.save_handler'));
        } else {
            $this->assertEquals('files', $storage->getSaveHandler()->getSaveHandlerName());
            $this->assertEquals('user', ini_get('session.save_handler'));
        }

        $this->assertEquals(sys_get_temp_dir(), ini_get('session.save_path'));
        $this->assertEquals('TESTING', ini_get('session.name'));
    }

    /**
     * @dataProvider savePathDataProvider
     */
    public function testConstructSavePath($savePath, $expectedSavePath, $path)
    {
        $handler = new NativeFileSessionHandler($savePath);
        $this->assertEquals($expectedSavePath, ini_get('session.save_path'));
        $this->assertTrue(is_dir(realpath($path)));

        rmdir($path);
    }

    public function savePathDataProvider()
    {
        $base = sys_get_temp_dir();

        return array(
            array("$base/foo", "$base/foo", "$base/foo"),
            array("5;$base/foo", "5;$base/foo", "$base/foo"),
            array("5;0600;$base/foo", "5;0600;$base/foo", "$base/foo"),
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructException()
    {
        $handler = new NativeFileSessionHandler('something;invalid;with;too-many-args');
    }

    public function testConstructDefault()
    {
        $path = ini_get('session.save_path');
        $storage = new NativeSessionStorage(array('name' => 'TESTING'), new NativeFileSessionHandler());

        $this->assertEquals($path, ini_get('session.save_path'));
    }
}
