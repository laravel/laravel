<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Pipeline;

use SplQueue;
use PredisTestCase;
use Predis\Profile\ServerProfile;

/**
 *
 */
class FireAndForgetExecutorTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testExecutorWithSingleConnection()
    {
        $executor = new FireAndForgetExecutor();
        $pipeline = $this->getCommandsQueue();

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->exactly(3))
                   ->method('writeCommand');
        $connection->expects($this->never())
                   ->method('readResponse');

        $replies = $executor->execute($connection, $pipeline);

        $this->assertTrue($pipeline->isEmpty());
        $this->assertEmpty($replies);
    }

    /**
     * @group disconnected
     */
    public function testExecutorWithReplicationConnection()
    {
        $executor = new FireAndForgetExecutor();
        $pipeline = $this->getCommandsQueue();

        $connection = $this->getMock('Predis\Connection\ReplicationConnectionInterface');
        $connection->expects($this->once())
                   ->method('switchTo')
                   ->with('master');
        $connection->expects($this->exactly(3))
                   ->method('writeCommand');
        $connection->expects($this->never())
                   ->method('readResponse');

        $replies = $executor->execute($connection, $pipeline);

        $this->assertTrue($pipeline->isEmpty());
        $this->assertEmpty($replies);
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Returns a list of queued command instances.
     *
     * @return SplQueue
     */
    protected function getCommandsQueue()
    {
        $profile = ServerProfile::getDevelopment();

        $pipeline = new SplQueue();
        $pipeline->enqueue($profile->createCommand('ping'));
        $pipeline->enqueue($profile->createCommand('ping'));
        $pipeline->enqueue($profile->createCommand('ping'));

        return $pipeline;
    }
}
