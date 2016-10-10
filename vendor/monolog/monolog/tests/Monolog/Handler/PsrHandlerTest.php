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
 * @covers Monolog\Handler\PsrHandler::handle
 */
class PsrHandlerTest extends TestCase
{
    public function logLevelProvider()
    {
        $levels = array();
        $monologLogger = new Logger('');

        foreach ($monologLogger->getLevels() as $levelName => $level) {
            $levels[] = array($levelName, $level);
        }

        return $levels;
    }

    /**
     * @dataProvider logLevelProvider
     */
    public function testHandlesAllLevels($levelName, $level)
    {
        $message = 'Hello, world! ' . $level;
        $context = array('foo' => 'bar', 'level' => $level);

        $psrLogger = $this->getMock('Psr\Log\NullLogger');
        $psrLogger->expects($this->once())
            ->method('log')
            ->with(strtolower($levelName), $message, $context);

        $handler = new PsrHandler($psrLogger);
        $handler->handle(array('level' => $level, 'level_name' => $levelName, 'message' => $message, 'context' => $context));
    }
}
