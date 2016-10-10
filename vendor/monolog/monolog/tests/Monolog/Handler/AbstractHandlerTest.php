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
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\WebProcessor;

class AbstractHandlerTest extends TestCase
{
    /**
     * @covers Monolog\Handler\AbstractHandler::__construct
     * @covers Monolog\Handler\AbstractHandler::getLevel
     * @covers Monolog\Handler\AbstractHandler::setLevel
     * @covers Monolog\Handler\AbstractHandler::getBubble
     * @covers Monolog\Handler\AbstractHandler::setBubble
     * @covers Monolog\Handler\AbstractHandler::getFormatter
     * @covers Monolog\Handler\AbstractHandler::setFormatter
     */
    public function testConstructAndGetSet()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler', array(Logger::WARNING, false));
        $this->assertEquals(Logger::WARNING, $handler->getLevel());
        $this->assertEquals(false, $handler->getBubble());

        $handler->setLevel(Logger::ERROR);
        $handler->setBubble(true);
        $handler->setFormatter($formatter = new LineFormatter);
        $this->assertEquals(Logger::ERROR, $handler->getLevel());
        $this->assertEquals(true, $handler->getBubble());
        $this->assertSame($formatter, $handler->getFormatter());
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::handleBatch
     */
    public function testHandleBatch()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler');
        $handler->expects($this->exactly(2))
            ->method('handle');
        $handler->handleBatch(array($this->getRecord(), $this->getRecord()));
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::isHandling
     */
    public function testIsHandling()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler', array(Logger::WARNING, false));
        $this->assertTrue($handler->isHandling($this->getRecord()));
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::__construct
     */
    public function testHandlesPsrStyleLevels()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler', array('warning', false));
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::DEBUG)));
        $handler->setLevel('debug');
        $this->assertTrue($handler->isHandling($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::getFormatter
     * @covers Monolog\Handler\AbstractHandler::getDefaultFormatter
     */
    public function testGetFormatterInitializesDefault()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler');
        $this->assertInstanceOf('Monolog\Formatter\LineFormatter', $handler->getFormatter());
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::pushProcessor
     * @covers Monolog\Handler\AbstractHandler::popProcessor
     * @expectedException LogicException
     */
    public function testPushPopProcessor()
    {
        $logger = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler');
        $processor1 = new WebProcessor;
        $processor2 = new WebProcessor;

        $logger->pushProcessor($processor1);
        $logger->pushProcessor($processor2);

        $this->assertEquals($processor2, $logger->popProcessor());
        $this->assertEquals($processor1, $logger->popProcessor());
        $logger->popProcessor();
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::pushProcessor
     * @expectedException InvalidArgumentException
     */
    public function testPushProcessorWithNonCallable()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler');

        $handler->pushProcessor(new \stdClass());
    }
}
