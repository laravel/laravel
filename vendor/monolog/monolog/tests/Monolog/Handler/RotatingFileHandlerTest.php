<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\TestCase;

/**
 * @covers Monolog\Handler\RotatingFileHandler
 */
class RotatingFileHandlerTest extends TestCase
{
    public function setUp()
    {
        $dir = __DIR__.'/Fixtures';
        chmod($dir, 0777);
        if (!is_writable($dir)) {
            $this->markTestSkipped($dir.' must be writeable to test the RotatingFileHandler.');
        }
    }

    public function testRotationCreatesNewFile()
    {
        touch(__DIR__.'/Fixtures/foo-'.date('Y-m-d', time() - 86400).'.rot');

        $handler = new RotatingFileHandler(__DIR__.'/Fixtures/foo.rot');
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord());

        $log = __DIR__.'/Fixtures/foo-'.date('Y-m-d').'.rot';
        $this->assertTrue(file_exists($log));
        $this->assertEquals('test', file_get_contents($log));
    }

    /**
     * @dataProvider rotationTests
     */
    public function testRotation($createFile)
    {
        touch($old1 = __DIR__.'/Fixtures/foo-'.date('Y-m-d', time() - 86400).'.rot');
        touch($old2 = __DIR__.'/Fixtures/foo-'.date('Y-m-d', time() - 86400 * 2).'.rot');
        touch($old3 = __DIR__.'/Fixtures/foo-'.date('Y-m-d', time() - 86400 * 3).'.rot');
        touch($old4 = __DIR__.'/Fixtures/foo-'.date('Y-m-d', time() - 86400 * 4).'.rot');

        $log = __DIR__.'/Fixtures/foo-'.date('Y-m-d').'.rot';

        if ($createFile) {
            touch($log);
        }

        $handler = new RotatingFileHandler(__DIR__.'/Fixtures/foo.rot', 2);
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord());

        $handler->close();

        $this->assertTrue(file_exists($log));
        $this->assertTrue(file_exists($old1));
        $this->assertEquals($createFile, file_exists($old2));
        $this->assertEquals($createFile, file_exists($old3));
        $this->assertEquals($createFile, file_exists($old4));
        $this->assertEquals('test', file_get_contents($log));
    }

    public function rotationTests()
    {
        return array(
            'Rotation is triggered when the file of the current day is not present'
                => array(true),
            'Rotation is not triggered when the file is already present'
                => array(false),
        );
    }

    public function testReuseCurrentFile()
    {
        $log = __DIR__.'/Fixtures/foo-'.date('Y-m-d').'.rot';
        file_put_contents($log, "foo");
        $handler = new RotatingFileHandler(__DIR__.'/Fixtures/foo.rot');
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord());
        $this->assertEquals('footest', file_get_contents($log));
    }

    public function tearDown()
    {
        foreach (glob(__DIR__.'/Fixtures/*.rot') as $file) {
            unlink($file);
        }
    }
}
