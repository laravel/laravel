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
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossed\ChannelLevelActivationStrategy;
use Psr\Log\LogLevel;

class FingersCrossedHandlerTest extends TestCase
{
    /**
     * @covers Monolog\Handler\FingersCrossedHandler::__construct
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     */
    public function testHandleBuffers()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        $this->assertFalse($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
        $handler->handle($this->getRecord(Logger::WARNING));
        $handler->close();
        $this->assertTrue($test->hasInfoRecords());
        $this->assertTrue(count($test->getRecords()) === 3);
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     */
    public function testHandleStopsBufferingAfterTrigger()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test);
        $handler->handle($this->getRecord(Logger::WARNING));
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->close();
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasDebugRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     * @covers Monolog\Handler\FingersCrossedHandler::reset
     */
    public function testHandleRestartBufferingAfterReset()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test);
        $handler->handle($this->getRecord(Logger::WARNING));
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->reset();
        $handler->handle($this->getRecord(Logger::INFO));
        $handler->close();
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     */
    public function testHandleRestartBufferingAfterBeingTriggeredWhenStopBufferingIsDisabled()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test, Logger::WARNING, 0, false, false);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::WARNING));
        $handler->handle($this->getRecord(Logger::INFO));
        $handler->close();
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     */
    public function testHandleBufferLimit()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test, Logger::WARNING, 2);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $this->assertTrue($test->hasInfoRecords());
        $this->assertFalse($test->hasDebugRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     */
    public function testHandleWithCallback()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler(function ($record, $handler) use ($test) {
                    return $test;
                });
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        $this->assertFalse($test->hasDebugRecords());
        $this->assertFalse($test->hasInfoRecords());
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasInfoRecords());
        $this->assertTrue(count($test->getRecords()) === 3);
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     * @expectedException RuntimeException
     */
    public function testHandleWithBadCallbackThrowsException()
    {
        $handler = new FingersCrossedHandler(function ($record, $handler) {
                    return 'foo';
                });
        $handler->handle($this->getRecord(Logger::WARNING));
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::isHandling
     */
    public function testIsHandlingAlways()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test, Logger::ERROR);
        $this->assertTrue($handler->isHandling($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::__construct
     * @covers Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy::__construct
     * @covers Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy::isHandlerActivated
     */
    public function testErrorLevelActivationStrategy()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test, new ErrorLevelActivationStrategy(Logger::WARNING));
        $handler->handle($this->getRecord(Logger::DEBUG));
        $this->assertFalse($test->hasDebugRecords());
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasDebugRecords());
        $this->assertTrue($test->hasWarningRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::__construct
     * @covers Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy::__construct
     * @covers Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy::isHandlerActivated
     */
    public function testErrorLevelActivationStrategyWithPsrLevel()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test, new ErrorLevelActivationStrategy('warning'));
        $handler->handle($this->getRecord(Logger::DEBUG));
        $this->assertFalse($test->hasDebugRecords());
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasDebugRecords());
        $this->assertTrue($test->hasWarningRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossed\ChannelLevelActivationStrategy::__construct
     * @covers Monolog\Handler\FingersCrossed\ChannelLevelActivationStrategy::isHandlerActivated
     */
    public function testChannelLevelActivationStrategy()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test, new ChannelLevelActivationStrategy(Logger::ERROR, array('othertest' => Logger::DEBUG)));
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertFalse($test->hasWarningRecords());
        $record = $this->getRecord(Logger::DEBUG);
        $record['channel'] = 'othertest';
        $handler->handle($record);
        $this->assertTrue($test->hasDebugRecords());
        $this->assertTrue($test->hasWarningRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossed\ChannelLevelActivationStrategy::__construct
     * @covers Monolog\Handler\FingersCrossed\ChannelLevelActivationStrategy::isHandlerActivated
     */
    public function testChannelLevelActivationStrategyWithPsrLevels()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test, new ChannelLevelActivationStrategy('error', array('othertest' => 'debug')));
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertFalse($test->hasWarningRecords());
        $record = $this->getRecord(Logger::DEBUG);
        $record['channel'] = 'othertest';
        $handler->handle($record);
        $this->assertTrue($test->hasDebugRecords());
        $this->assertTrue($test->hasWarningRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::handle
     */
    public function testHandleUsesProcessors()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test, Logger::INFO);
        $handler->pushProcessor(function ($record) {
            $record['extra']['foo'] = true;

            return $record;
        });
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $records = $test->getRecords();
        $this->assertTrue($records[0]['extra']['foo']);
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::close
     */
    public function testPassthruOnClose()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test, new ErrorLevelActivationStrategy(Logger::WARNING), 0, true, true, Logger::INFO);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        $handler->close();
        $this->assertFalse($test->hasDebugRecords());
        $this->assertTrue($test->hasInfoRecords());
    }

    /**
     * @covers Monolog\Handler\FingersCrossedHandler::close
     */
    public function testPsrLevelPassthruOnClose()
    {
        $test = new TestHandler();
        $handler = new FingersCrossedHandler($test, new ErrorLevelActivationStrategy(Logger::WARNING), 0, true, true, LogLevel::INFO);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        $handler->close();
        $this->assertFalse($test->hasDebugRecords());
        $this->assertTrue($test->hasInfoRecords());
    }
}
