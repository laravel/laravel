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

use PredisTestCase;
use Predis\Client;
use Predis\ClientException;
use Predis\Profile\ServerProfile;

/**
 *
 */
class PipelineContextTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testConstructorWithoutOptions()
    {
        $client = new Client();
        $pipeline = new PipelineContext($client);

        $this->assertSame($client, $pipeline->getClient());
        $this->assertInstanceOf('Predis\Pipeline\StandardExecutor', $pipeline->getExecutor());
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithExecutorArgument()
    {
        $client = new Client();
        $executor = $this->getMock('Predis\Pipeline\PipelineExecutorInterface');

        $pipeline = new PipelineContext($client, $executor);
        $this->assertSame($executor, $pipeline->getExecutor());
    }

    /**
     * @group disconnected
     */
    public function testCallDoesNotSendCommandsWithoutExecute()
    {
        $executor = $this->getMock('Predis\Pipeline\PipelineExecutorInterface');
        $executor->expects($this->never())->method('executor');

        $pipeline = new PipelineContext(new Client(), $executor);

        $pipeline->echo('one');
        $pipeline->echo('two');
        $pipeline->echo('three');
    }

    /**
     * @group disconnected
     */
    public function testCallReturnsPipelineForFluentInterface()
    {
        $executor = $this->getMock('Predis\Pipeline\PipelineExecutorInterface');
        $executor->expects($this->never())->method('executor');

        $pipeline = new PipelineContext(new Client(), $executor);

        $this->assertSame($pipeline, $pipeline->echo('one'));
        $this->assertSame($pipeline, $pipeline->echo('one')->echo('two')->echo('three'));
    }

    /**
     * @group disconnected
     */
     public function testExecuteReturnsPipelineForFluentInterface()
     {
        $profile = ServerProfile::getDefault();
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');

        $pipeline = new PipelineContext(new Client($connection));
        $command = $profile->createCommand('echo', array('one'));

        $this->assertSame($pipeline, $pipeline->executeCommand($command));
     }

    /**
     * @group disconnected
     */
    public function testExecuteCommandDoesNotSendCommandsWithoutExecute()
    {
        $profile = ServerProfile::getDefault();

        $executor = $this->getMock('Predis\Pipeline\PipelineExecutorInterface');
        $executor->expects($this->never())->method('executor');

        $pipeline = new PipelineContext(new Client(), $executor);

        $pipeline->executeCommand($profile->createCommand('echo', array('one')));
        $pipeline->executeCommand($profile->createCommand('echo', array('two')));
        $pipeline->executeCommand($profile->createCommand('echo', array('three')));
    }

    /**
     * @group disconnected
     */
    public function testExecuteWithEmptyBuffer()
    {
        $executor = $this->getMock('Predis\Pipeline\PipelineExecutorInterface');
        $executor->expects($this->never())->method('executor');

        $pipeline = new PipelineContext(new Client(), $executor);

        $this->assertSame(array(), $pipeline->execute());
    }

    /**
     * @group disconnected
     */
    public function testExecuteWithFilledBuffer()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->exactly(3))
                   ->method('writeCommand');
        $connection->expects($this->exactly(3))
                   ->method('readResponse')
                   ->will($this->returnCallback($this->getReadCallback()));

        $pipeline = new PipelineContext(new Client($connection));

        $pipeline->echo('one');
        $pipeline->echo('two');
        $pipeline->echo('three');

        $pipeline->flushPipeline();

        $this->assertSame(array('one', 'two', 'three'), $pipeline->execute());
    }

    /**
     * @group disconnected
     */
    public function testFlushWithFalseArgumentDiscardsBuffer()
    {
        $executor = $this->getMock('Predis\Pipeline\PipelineExecutorInterface');
        $executor->expects($this->never())->method('executor');

        $pipeline = new PipelineContext(new Client(), $executor);

        $pipeline->echo('one');
        $pipeline->echo('two');
        $pipeline->echo('three');

        $pipeline->flushPipeline(false);

        $this->assertSame(array(), $pipeline->execute());
    }

    /**
     * @group disconnected
     */
    public function testFlushHandlesPartialBuffers()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->exactly(4))
                   ->method('writeCommand');
        $connection->expects($this->exactly(4))
                   ->method('readResponse')
                   ->will($this->returnCallback($this->getReadCallback()));

        $pipeline = new PipelineContext(new Client($connection));

        $pipeline->echo('one');
        $pipeline->echo('two');
        $pipeline->flushPipeline();
        $pipeline->echo('three');
        $pipeline->echo('four');

        $this->assertSame(array('one', 'two', 'three', 'four'), $pipeline->execute());
    }

    /**
     * @group disconnected
     */
    public function testExecuteAcceptsCallableArgument()
    {
        $test = $this;
        $pipeline = new PipelineContext(new Client());

        $callable = function ($pipe) use ($test, $pipeline) {
            $test->assertSame($pipeline, $pipe);
            $pipe->flushPipeline(false);
        };

        $pipeline->execute($callable);
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     */
    public function testExecuteDoesNotAcceptNonCallableArgument()
    {
        $noncallable = new \stdClass();

        $pipeline = new PipelineContext(new Client());
        $pipeline->execute($noncallable);
    }

    /**
     * @group disconnected
     * @expectedException Predis\ClientException
     */
    public function testExecuteInsideCallableArgumentThrowsException()
    {
        $pipeline = new PipelineContext(new Client());

        $pipeline->execute(function ($pipe) {
            $pipe->execute();
        });
    }

    /**
     * @group disconnected
     */
    public function testExecuteWithCallableArgumentRunsPipelineInCallable()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->exactly(4))
                   ->method('writeCommand');
        $connection->expects($this->exactly(4))
                   ->method('readResponse')
                   ->will($this->returnCallback($this->getReadCallback()));

        $pipeline = new PipelineContext(new Client($connection));

        $replies = $pipeline->execute(function ($pipe) {
            $pipe->echo('one');
            $pipe->echo('two');
            $pipe->echo('three');
            $pipe->echo('four');
        });

        $this->assertSame(array('one', 'two', 'three', 'four'), $replies);
    }

    /**
     * @group disconnected
     */
    public function testExecuteWithCallableArgumentHandlesExceptions()
    {
        $exception = null;

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->never())->method('writeCommand');
        $connection->expects($this->never())->method('readResponse');

        $pipeline = new PipelineContext(new Client($connection));

        $exception = null;
        $replies = null;

        try {
            $replies = $pipeline->execute(function ($pipe) {
                $pipe->echo('one');
                throw new ClientException('TEST');
                $pipe->echo('two');
            });
        } catch (\Exception $exception) {
            // NOOP
        }

        $this->assertInstanceOf('Predis\ClientException', $exception);
        $this->assertSame('TEST', $exception->getMessage());
        $this->assertNull($replies);
    }

    // ******************************************************************** //
    // ---- INTEGRATION TESTS --------------------------------------------- //
    // ******************************************************************** //

    /**
     * @group connected
     */
    public function testIntegrationWithFluentInterface()
    {
        $pipeline = $this->getClient()->pipeline();

        $results = $pipeline->echo('one')
                            ->echo('two')
                            ->echo('three')
                            ->execute();

        $this->assertSame(array('one', 'two', 'three'), $results);
    }

    /**
     * @group connected
     */
    public function testIntegrationWithCallableBlock()
    {
        $client = $this->getClient();

        $results = $client->pipeline(function ($pipe) {
            $pipe->set('foo', 'bar');
            $pipe->get('foo');
        });

        $this->assertSame(array(true, 'bar'), $results);
        $this->assertTrue($client->exists('foo'));
    }

    /**
     * @group connected
     */
    public function testOutOfBandMessagesInsidePipeline()
    {
        $oob = null;
        $client = $this->getClient();

        $results = $client->pipeline(function ($pipe) use (&$oob) {
            $pipe->set('foo', 'bar');
            $oob = $pipe->getClient()->echo('oob message');
            $pipe->get('foo');
        });

        $this->assertSame(array(true, 'bar'), $results);
        $this->assertSame('oob message', $oob);
        $this->assertTrue($client->exists('foo'));
    }

    /**
     * @group connected
     */
    public function testIntegrationWithClientExceptionInCallableBlock()
    {
        $exception = null;

        $client = $this->getClient();

        try {
            $client->pipeline(function ($pipe) {
                $pipe->set('foo', 'bar');
                throw new ClientException('TEST');
            });
        } catch (\Exception $exception) {
            // NOOP
        }

        $this->assertInstanceOf('Predis\ClientException', $exception);
        $this->assertSame('TEST', $exception->getMessage());
        $this->assertFalse($client->exists('foo'));
    }

    /**
     * @group connected
     */
    public function testIntegrationWithServerExceptionInCallableBlock()
    {
        $exception = null;

        $client = $this->getClient();

        try {
            $client->pipeline(function ($pipe) {
                $pipe->set('foo', 'bar');
                // LPUSH on a string key fails, but won't stop
                // the pipeline to send the commands.
                $pipe->lpush('foo', 'bar');
                $pipe->set('hoge', 'piyo');
            });
        } catch (\Exception $exception) {
            // NOOP
        }

        $this->assertInstanceOf('Predis\ServerException', $exception);
        $this->assertTrue($client->exists('foo'));
        $this->assertTrue($client->exists('hoge'));
    }

    /**
     * @group connected
     */
    public function testIntegrationWithServerErrorInCallableBlock()
    {
        $client = $this->getClient(array(), array('exceptions' => false));

        $results = $client->pipeline(function ($pipe) {
            $pipe->set('foo', 'bar');
            $pipe->lpush('foo', 'bar'); // LPUSH on a string key fails.
            $pipe->get('foo');
        });

        $this->assertTrue($results[0]);
        $this->assertInstanceOf('Predis\ResponseError', $results[1]);
        $this->assertSame('bar', $results[2]);
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Returns a client instance connected to the specified Redis
     * server instance to perform integration tests.
     *
     * @param  array  $parameters Additional connection parameters.
     * @param  array  $options    Additional client options.
     * @return Client
     */
    protected function getClient(array $parameters = array(), array $options = array())
    {
        return $this->createClient($parameters, $options);
    }

    /**
     * Helper method that returns a callback used to emulate a reply
     * to an ECHO command.
     *
     * @return \Closure
     */
    protected function getReadCallback()
    {
        return function ($command) {
            if (($id = $command->getId()) !== 'ECHO') {
                throw new \InvalidArgumentException("Expected ECHO, got {$id}");
            }

            list($echoed) = $command->getArguments();

            return $echoed;
        };
    }
}
