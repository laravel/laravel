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

class GroupHandlerTest extends TestCase
{
    /**
     * @covers Monolog\Handler\GroupHandler::__construct
     * @expectedException InvalidArgumentException
     */
    public function testConstructorOnlyTakesHandler()
    {
        new GroupHandler(array(new TestHandler(), "foo"));
    }

    /**
     * @covers Monolog\Handler\GroupHandler::__construct
     * @covers Monolog\Handler\GroupHandler::handle
     */
    public function testHandle()
    {
        $testHandlers = array(new TestHandler(), new TestHandler());
        $handler = new GroupHandler($testHandlers);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        foreach ($testHandlers as $test) {
            $this->assertTrue($test->hasDebugRecords());
            $this->assertTrue($test->hasInfoRecords());
            $this->assertTrue(count($test->getRecords()) === 2);
        }
    }

    /**
     * @covers Monolog\Handler\GroupHandler::handleBatch
     */
    public function testHandleBatch()
    {
        $testHandlers = array(new TestHandler(), new TestHandler());
        $handler = new GroupHandler($testHandlers);
        $handler->handleBatch(array($this->getRecord(Logger::DEBUG), $this->getRecord(Logger::INFO)));
        foreach ($testHandlers as $test) {
            $this->assertTrue($test->hasDebugRecords());
            $this->assertTrue($test->hasInfoRecords());
            $this->assertTrue(count($test->getRecords()) === 2);
        }
    }

    /**
     * @covers Monolog\Handler\GroupHandler::isHandling
     */
    public function testIsHandling()
    {
        $testHandlers = array(new TestHandler(Logger::ERROR), new TestHandler(Logger::WARNING));
        $handler = new GroupHandler($testHandlers);
        $this->assertTrue($handler->isHandling($this->getRecord(Logger::ERROR)));
        $this->assertTrue($handler->isHandling($this->getRecord(Logger::WARNING)));
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Monolog\Handler\GroupHandler::handle
     */
    public function testHandleUsesProcessors()
    {
        $test = new TestHandler();
        $handler = new GroupHandler(array($test));
        $handler->pushProcessor(function ($record) {
            $record['extra']['foo'] = true;

            return $record;
        });
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $records = $test->getRecords();
        $this->assertTrue($records[0]['extra']['foo']);
    }
}
