<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Protocol\Text;

use PredisTestCase;
use Predis\ResponseQueued;

/**
 *
 */
class ResponseStatusHandlerTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testOk()
    {
        $handler = new ResponseStatusHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->never())->method('readBytes');

        $this->assertTrue($handler->handle($connection, 'OK'));
    }

    /**
     * @group disconnected
     */
    public function testQueued()
    {
        $handler = new ResponseStatusHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->never())->method('readBytes');

        $this->assertInstanceOf('Predis\ResponseQueued', $handler->handle($connection, 'QUEUED'));
    }

    /**
     * @group disconnected
     */
    public function testPlainString()
    {
        $handler = new ResponseStatusHandler();

        $connection = $this->getMock('Predis\Connection\ComposableConnectionInterface');

        $connection->expects($this->never())->method('readLine');
        $connection->expects($this->never())->method('readBytes');

        $this->assertSame('Background saving started', $handler->handle($connection, 'Background saving started'));
    }
}
