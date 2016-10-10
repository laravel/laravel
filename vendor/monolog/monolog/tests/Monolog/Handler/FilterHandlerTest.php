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

use Monolog\Logger;
use Monolog\TestCase;

class FilterHandlerTest extends TestCase
{
    /**
     * @covers Monolog\Handler\FilterHandler::isHandling
     */
    public function testIsHandling()
    {
        $test    = new TestHandler();
        $handler = new FilterHandler($test, Logger::INFO, Logger::NOTICE);
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::DEBUG)));
        $this->assertTrue($handler->isHandling($this->getRecord(Logger::INFO)));
        $this->assertTrue($handler->isHandling($this->getRecord(Logger::NOTICE)));
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::WARNING)));
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::ERROR)));
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::CRITICAL)));
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::ALERT)));
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::EMERGENCY)));
    }

    /**
     * @covers Monolog\Handler\FilterHandler::handle
     * @covers Monolog\Handler\FilterHandler::setAcceptedLevels
     * @covers Monolog\Handler\FilterHandler::isHandling
     */
    public function testHandleProcessOnlyNeededLevels()
    {
        $test    = new TestHandler();
        $handler = new FilterHandler($test, Logger::INFO, Logger::NOTICE);

        $handler->handle($this->getRecord(Logger::DEBUG));
        $this->assertFalse($test->hasDebugRecords());

        $handler->handle($this->getRecord(Logger::INFO));
        $this->assertTrue($test->hasInfoRecords());
        $handler->handle($this->getRecord(Logger::NOTICE));
        $this->assertTrue($test->hasNoticeRecords());

        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertFalse($test->hasWarningRecords());
        $handler->handle($this->getRecord(Logger::ERROR));
        $this->assertFalse($test->hasErrorRecords());
        $handler->handle($this->getRecord(Logger::CRITICAL));
        $this->assertFalse($test->hasCriticalRecords());
        $handler->handle($this->getRecord(Logger::ALERT));
        $this->assertFalse($test->hasAlertRecords());
        $handler->handle($this->getRecord(Logger::EMERGENCY));
        $this->assertFalse($test->hasEmergencyRecords());

        $test    = new TestHandler();
        $handler = new FilterHandler($test, array(Logger::INFO, Logger::ERROR));

        $handler->handle($this->getRecord(Logger::DEBUG));
        $this->assertFalse($test->hasDebugRecords());
        $handler->handle($this->getRecord(Logger::INFO));
        $this->assertTrue($test->hasInfoRecords());
        $handler->handle($this->getRecord(Logger::NOTICE));
        $this->assertFalse($test->hasNoticeRecords());
        $handler->handle($this->getRecord(Logger::ERROR));
        $this->assertTrue($test->hasErrorRecords());
        $handler->handle($this->getRecord(Logger::CRITICAL));
        $this->assertFalse($test->hasCriticalRecords());
    }

    /**
     * @covers Monolog\Handler\FilterHandler::setAcceptedLevels
     * @covers Monolog\Handler\FilterHandler::getAcceptedLevels
     */
    public function testAcceptedLevelApi()
    {
        $test    = new TestHandler();
        $handler = new FilterHandler($test);

        $levels = array(Logger::INFO, Logger::ERROR);
        $handler->setAcceptedLevels($levels);
        $this->assertSame($levels, $handler->getAcceptedLevels());

        $handler->setAcceptedLevels(array('info', 'error'));
        $this->assertSame($levels, $handler->getAcceptedLevels());

        $levels = array(Logger::CRITICAL, Logger::ALERT, Logger::EMERGENCY);
        $handler->setAcceptedLevels(Logger::CRITICAL, Logger::EMERGENCY);
        $this->assertSame($levels, $handler->getAcceptedLevels());

        $handler->setAcceptedLevels('critical', 'emergency');
        $this->assertSame($levels, $handler->getAcceptedLevels());
    }

    /**
     * @covers Monolog\Handler\FilterHandler::handle
     */
    public function testHandleUsesProcessors()
    {
        $test    = new TestHandler();
        $handler = new FilterHandler($test, Logger::DEBUG, Logger::EMERGENCY);
        $handler->pushProcessor(
            function ($record) {
                $record['extra']['foo'] = true;

                return $record;
            }
        );
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $records = $test->getRecords();
        $this->assertTrue($records[0]['extra']['foo']);
    }

    /**
     * @covers Monolog\Handler\FilterHandler::handle
     */
    public function testHandleRespectsBubble()
    {
        $test = new TestHandler();

        $handler = new FilterHandler($test, Logger::INFO, Logger::NOTICE, false);
        $this->assertTrue($handler->handle($this->getRecord(Logger::INFO)));
        $this->assertFalse($handler->handle($this->getRecord(Logger::WARNING)));

        $handler = new FilterHandler($test, Logger::INFO, Logger::NOTICE, true);
        $this->assertFalse($handler->handle($this->getRecord(Logger::INFO)));
        $this->assertFalse($handler->handle($this->getRecord(Logger::WARNING)));
    }

    /**
     * @covers Monolog\Handler\FilterHandler::handle
     */
    public function testHandleWithCallback()
    {
        $test    = new TestHandler();
        $handler = new FilterHandler(
            function ($record, $handler) use ($test) {
                return $test;
            }, Logger::INFO, Logger::NOTICE, false
        );
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        $this->assertFalse($test->hasDebugRecords());
        $this->assertTrue($test->hasInfoRecords());
    }

    /**
     * @covers Monolog\Handler\FilterHandler::handle
     * @expectedException \RuntimeException
     */
    public function testHandleWithBadCallbackThrowsException()
    {
        $handler = new FilterHandler(
            function ($record, $handler) {
                return 'foo';
            }
        );
        $handler->handle($this->getRecord(Logger::WARNING));
    }
}
