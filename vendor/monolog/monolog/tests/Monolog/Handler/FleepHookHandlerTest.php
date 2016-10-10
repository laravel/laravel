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

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\TestCase;

/**
 * @coversDefaultClass \Monolog\Handler\FleepHookHandler
 */
class FleepHookHandlerTest extends TestCase
{
    /**
     * Default token to use in tests
     */
    const TOKEN = '123abc';

    /**
     * @var FleepHookHandler
     */
    private $handler;

    public function setUp()
    {
        parent::setUp();

        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('This test requires openssl extension to run');
        }

        // Create instances of the handler and logger for convenience
        $this->handler = new FleepHookHandler(self::TOKEN);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorSetsExpectedDefaults()
    {
        $this->assertEquals(Logger::DEBUG, $this->handler->getLevel());
        $this->assertEquals(true, $this->handler->getBubble());
    }

    /**
     * @covers ::getDefaultFormatter
     */
    public function testHandlerUsesLineFormatterWhichIgnoresEmptyArrays()
    {
        $record = array(
            'message' => 'msg',
            'context' => array(),
            'level' => Logger::DEBUG,
            'level_name' => Logger::getLevelName(Logger::DEBUG),
            'channel' => 'channel',
            'datetime' => new \DateTime(),
            'extra' => array(),
        );

        $expectedFormatter = new LineFormatter(null, null, true, true);
        $expected = $expectedFormatter->format($record);

        $handlerFormatter = $this->handler->getFormatter();
        $actual = $handlerFormatter->format($record);

        $this->assertEquals($expected, $actual, 'Empty context and extra arrays should not be rendered');
    }

    /**
     * @covers ::__construct
     */
    public function testConnectionStringisConstructedCorrectly()
    {
        $this->assertEquals('ssl://' . FleepHookHandler::FLEEP_HOST . ':443', $this->handler->getConnectionString());
    }
}
