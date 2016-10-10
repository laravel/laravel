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
 * @covers Monolog\Handler\NullHandler::handle
 */
class NullHandlerTest extends TestCase
{
    public function testHandle()
    {
        $handler = new NullHandler();
        $this->assertTrue($handler->handle($this->getRecord()));
    }

    public function testHandleLowerLevelRecord()
    {
        $handler = new NullHandler(Logger::WARNING);
        $this->assertFalse($handler->handle($this->getRecord(Logger::DEBUG)));
    }
}
