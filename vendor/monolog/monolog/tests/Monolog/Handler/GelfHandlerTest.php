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

use Gelf\Message;
use Monolog\TestCase;
use Monolog\Logger;
use Monolog\Formatter\GelfMessageFormatter;

class GelfHandlerTest extends TestCase
{
    public function setUp()
    {
        if (!class_exists('Gelf\Publisher') || !class_exists('Gelf\Message')) {
            $this->markTestSkipped("graylog2/gelf-php not installed");
        }
    }

    /**
     * @covers Monolog\Handler\GelfHandler::__construct
     */
    public function testConstruct()
    {
        $handler = new GelfHandler($this->getMessagePublisher());
        $this->assertInstanceOf('Monolog\Handler\GelfHandler', $handler);
    }

    protected function getHandler($messagePublisher)
    {
        $handler = new GelfHandler($messagePublisher);

        return $handler;
    }

    protected function getMessagePublisher()
    {
        return $this->getMock('Gelf\Publisher', array('publish'), array(), '', false);
    }

    public function testDebug()
    {
        $record = $this->getRecord(Logger::DEBUG, "A test debug message");
        $expectedMessage = new Message();
        $expectedMessage
            ->setLevel(7)
            ->setFacility("test")
            ->setShortMessage($record['message'])
            ->setTimestamp($record['datetime'])
        ;

        $messagePublisher = $this->getMessagePublisher();
        $messagePublisher->expects($this->once())
            ->method('publish')
            ->with($expectedMessage);

        $handler = $this->getHandler($messagePublisher);

        $handler->handle($record);
    }

    public function testWarning()
    {
        $record = $this->getRecord(Logger::WARNING, "A test warning message");
        $expectedMessage = new Message();
        $expectedMessage
            ->setLevel(4)
            ->setFacility("test")
            ->setShortMessage($record['message'])
            ->setTimestamp($record['datetime'])
        ;

        $messagePublisher = $this->getMessagePublisher();
        $messagePublisher->expects($this->once())
            ->method('publish')
            ->with($expectedMessage);

        $handler = $this->getHandler($messagePublisher);

        $handler->handle($record);
    }

    public function testInjectedGelfMessageFormatter()
    {
        $record = $this->getRecord(Logger::WARNING, "A test warning message");
        $record['extra']['blarg'] = 'yep';
        $record['context']['from'] = 'logger';

        $expectedMessage = new Message();
        $expectedMessage
            ->setLevel(4)
            ->setFacility("test")
            ->setHost("mysystem")
            ->setShortMessage($record['message'])
            ->setTimestamp($record['datetime'])
            ->setAdditional("EXTblarg", 'yep')
            ->setAdditional("CTXfrom", 'logger')
        ;

        $messagePublisher = $this->getMessagePublisher();
        $messagePublisher->expects($this->once())
            ->method('publish')
            ->with($expectedMessage);

        $handler = $this->getHandler($messagePublisher);
        $handler->setFormatter(new GelfMessageFormatter('mysystem', 'EXT', 'CTX'));
        $handler->handle($record);
    }
}
