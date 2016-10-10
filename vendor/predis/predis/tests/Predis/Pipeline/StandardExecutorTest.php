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
use Predis\ResponseError;
use Predis\ResponseObjectInterface;
use Predis\Profile\ServerProfile;

/**
 *
 */
class StandardExecutorTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testExecutorWithSingleConnection()
    {
        $executor = new StandardExecutor();
        $pipeline = $this->getCommandsQueue();

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->exactly(3))
                   ->method('writeCommand');
        $connection->expects($this->exactly(3))
                   ->method('readResponse')
                   ->will($this->returnValue('PONG'));

        $replies = $executor->execute($connection, $pipeline);

        $this->assertTrue($pipeline->isEmpty());
        $this->assertSame(array(true, true, true), $replies);
    }

    /**
     * @group disconnected
     */
    public function testExecutorWithReplicationConnection()
    {
        $executor = new StandardExecutor();
        $pipeline = $this->getCommandsQueue();

        $connection = $this->getMock('Predis\Connection\ReplicationConnectionInterface');
        $connection->expects($this->once())
                   ->method('switchTo')
                   ->with('master');
        $connection->expects($this->exactly(3))
                   ->method('writeCommand');
        $connection->expects($this->exactly(3))
                   ->method('readResponse')
                   ->will($this->returnValue('PONG'));

        $replies = $executor->execute($connection, $pipeline);

        $this->assertTrue($pipeline->isEmpty());
        $this->assertSame(array(true, true, true), $replies);
    }

    /**
     * @group disconnected
     */
    public function testExecutorDoesNotParseResponseObjects()
    {
        $executor = new StandardExecutor();
        $response = $this->getMock('Predis\ResponseObjectInterface');

        $this->simpleResponseObjectTest($executor, $response);
    }

    /**
     * @group disconnected
     */
    public function testExecutorCanReturnRedisErrors()
    {
        $executor = new StandardExecutor(false);
        $response = $this->getMock('Predis\ResponseErrorInterface');

        $this->simpleResponseObjectTest($executor, $response);
    }

    /**
     * @group disconnected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR Test error
     */
    public function testExecutorCanThrowExceptions()
    {
        $executor = new StandardExecutor(true);
        $pipeline = $this->getCommandsQueue();
        $error = new ResponseError('ERR Test error');

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())
                   ->method('readResponse')
                   ->will($this->returnValue($error));

        $executor->execute($connection, $pipeline);
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Executes a test for the Predis\ResponseObjectInterface type.
     *
     * @param PipelineExecutorInterface $executor
     * @param ResponseObjectInterface   $response
     */
    protected function simpleResponseObjectTest(PipelineExecutorInterface $executor, ResponseObjectInterface $response)
    {
        $pipeline = new SplQueue();

        $command = $this->getMock('Predis\Command\CommandInterface');
        $command->expects($this->never())
                ->method('parseResponse');

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())
                   ->method('writeCommand');
        $connection->expects($this->once())
                   ->method('readResponse')
                   ->will($this->returnValue($response));

        $pipeline->enqueue($command);
        $replies = $executor->execute($connection, $pipeline);

        $this->assertTrue($pipeline->isEmpty());
        $this->assertSame(array($response), $replies);
    }

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
