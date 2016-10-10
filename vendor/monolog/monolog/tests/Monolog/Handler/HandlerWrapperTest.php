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
 * @author Alexey Karapetov <alexey@karapetov.com>
 */
class HandlerWrapperTest extends TestCase
{
    /**
     * @var HandlerWrapper
     */
    private $wrapper;

    private $handler;

    public function setUp()
    {
        parent::setUp();
        $this->handler = $this->getMock('Monolog\\Handler\\HandlerInterface');
        $this->wrapper = new HandlerWrapper($this->handler);
    }

    /**
     * @return array
     */
    public function trueFalseDataProvider()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * @param $result
     * @dataProvider trueFalseDataProvider
     */
    public function testIsHandling($result)
    {
        $record = $this->getRecord();
        $this->handler->expects($this->once())
            ->method('isHandling')
            ->with($record)
            ->willReturn($result);

        $this->assertEquals($result, $this->wrapper->isHandling($record));
    }

    /**
     * @param $result
     * @dataProvider trueFalseDataProvider
     */
    public function testHandle($result)
    {
        $record = $this->getRecord();
        $this->handler->expects($this->once())
            ->method('handle')
            ->with($record)
            ->willReturn($result);

        $this->assertEquals($result, $this->wrapper->handle($record));
    }

    /**
     * @param $result
     * @dataProvider trueFalseDataProvider
     */
    public function testHandleBatch($result)
    {
        $records = $this->getMultipleRecords();
        $this->handler->expects($this->once())
            ->method('handleBatch')
            ->with($records)
            ->willReturn($result);

        $this->assertEquals($result, $this->wrapper->handleBatch($records));
    }

    public function testPushProcessor()
    {
        $processor = function () {};
        $this->handler->expects($this->once())
            ->method('pushProcessor')
            ->with($processor);

        $this->assertEquals($this->wrapper, $this->wrapper->pushProcessor($processor));
    }

    public function testPopProcessor()
    {
        $processor = function () {};
        $this->handler->expects($this->once())
            ->method('popProcessor')
            ->willReturn($processor);

        $this->assertEquals($processor, $this->wrapper->popProcessor());
    }

    public function testSetFormatter()
    {
        $formatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $this->handler->expects($this->once())
            ->method('setFormatter')
            ->with($formatter);

        $this->assertEquals($this->wrapper, $this->wrapper->setFormatter($formatter));
    }

    public function testGetFormatter()
    {
        $formatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $this->handler->expects($this->once())
            ->method('getFormatter')
            ->willReturn($formatter);

        $this->assertEquals($formatter, $this->wrapper->getFormatter());
    }
}
