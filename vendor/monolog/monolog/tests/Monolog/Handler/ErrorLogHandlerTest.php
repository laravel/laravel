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

function error_log()
{
    $GLOBALS['error_log'][] = func_get_args();
}

class ErrorLogHandlerTest extends TestCase
{
    protected function setUp()
    {
        $GLOBALS['error_log'] = array();
    }

    /**
     * @covers Monolog\Handler\ErrorLogHandler::__construct
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The given message type "42" is not supported
     */
    public function testShouldNotAcceptAnInvalidTypeOnContructor()
    {
        new ErrorLogHandler(42);
    }

    /**
     * @covers Monolog\Handler\ErrorLogHandler::write
     */
    public function testShouldLogMessagesUsingErrorLogFuncion()
    {
        $type = ErrorLogHandler::OPERATING_SYSTEM;
        $handler = new ErrorLogHandler($type);
        $handler->setFormatter(new LineFormatter('%channel%.%level_name%: %message% %context% %extra%', null, true));
        $handler->handle($this->getRecord(Logger::ERROR, "Foo\nBar\r\n\r\nBaz"));

        $this->assertSame("test.ERROR: Foo\nBar\r\n\r\nBaz [] []", $GLOBALS['error_log'][0][0]);
        $this->assertSame($GLOBALS['error_log'][0][1], $type);

        $handler = new ErrorLogHandler($type, Logger::DEBUG, true, true);
        $handler->setFormatter(new LineFormatter(null, null, true));
        $handler->handle($this->getRecord(Logger::ERROR, "Foo\nBar\r\n\r\nBaz"));

        $this->assertStringMatchesFormat('[%s] test.ERROR: Foo', $GLOBALS['error_log'][1][0]);
        $this->assertSame($GLOBALS['error_log'][1][1], $type);

        $this->assertStringMatchesFormat('Bar', $GLOBALS['error_log'][2][0]);
        $this->assertSame($GLOBALS['error_log'][2][1], $type);

        $this->assertStringMatchesFormat('Baz [] []', $GLOBALS['error_log'][3][0]);
        $this->assertSame($GLOBALS['error_log'][3][1], $type);
    }
}
