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
use Monolog\Logger;

/**
 * @covers Monolog\Handler\FirePHPHandler
 */
class FirePHPHandlerTest extends TestCase
{
    public function setUp()
    {
        TestFirePHPHandler::reset();
        $_SERVER['HTTP_USER_AGENT'] = 'Monolog Test; FirePHP/1.0';
    }

    public function testHeaders()
    {
        $handler = new TestFirePHPHandler;
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::WARNING));

        $expected = array(
            'X-Wf-Protocol-1'    => 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2',
            'X-Wf-1-Structure-1' => 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1',
            'X-Wf-1-Plugin-1'    => 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3',
            'X-Wf-1-1-1-1'       => 'test',
            'X-Wf-1-1-1-2'       => 'test',
        );

        $this->assertEquals($expected, $handler->getHeaders());
    }

    public function testConcurrentHandlers()
    {
        $handler = new TestFirePHPHandler;
        $handler->setFormatter($this->getIdentityFormatter());
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::WARNING));

        $handler2 = new TestFirePHPHandler;
        $handler2->setFormatter($this->getIdentityFormatter());
        $handler2->handle($this->getRecord(Logger::DEBUG));
        $handler2->handle($this->getRecord(Logger::WARNING));

        $expected = array(
            'X-Wf-Protocol-1'    => 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2',
            'X-Wf-1-Structure-1' => 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1',
            'X-Wf-1-Plugin-1'    => 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3',
            'X-Wf-1-1-1-1'       => 'test',
            'X-Wf-1-1-1-2'       => 'test',
        );

        $expected2 = array(
            'X-Wf-1-1-1-3'       => 'test',
            'X-Wf-1-1-1-4'       => 'test',
        );

        $this->assertEquals($expected, $handler->getHeaders());
        $this->assertEquals($expected2, $handler2->getHeaders());
    }
}

class TestFirePHPHandler extends FirePHPHandler
{
    protected $headers = array();

    public static function reset()
    {
        self::$initialized = false;
        self::$sendHeaders = true;
        self::$messageIndex = 1;
    }

    protected function sendHeader($header, $content)
    {
        $this->headers[$header] = $content;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
