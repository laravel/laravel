<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Transaction;

use PredisTestCase;
use Predis\Client;
use Predis\ResponseQueued;
use Predis\ResponseError;
use Predis\ServerException;
use Predis\Command\CommandInterface;

/**
 * @group realm-transaction
 */
class MultiExecContextTest extends PredisTestCase
{
    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage The current profile does not support MULTI, EXEC and DISCARD
     */
    public function testThrowsExceptionOnUnsupportedMultiExecInProfile()
    {
        $profile = $this->getMock('Predis\Profile\ServerProfileInterface');
        $profile->expects($this->once())
                ->method('supportsCommands')
                ->with(array('MULTI', 'EXEC', 'DISCARD'))
                ->will($this->returnValue(false));

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $client = new Client($connection, array('profile' => $profile));

        $tx = new MultiExecContext($client);
    }

    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage The current profile does not support WATCH and UNWATCH
     */
    public function testThrowsExceptionOnUnsupportedWatchUnwatchInProfile()
    {
        $profile = $this->getMock('Predis\Profile\ServerProfileInterface');
        $profile->expects($this->at(0))
                ->method('supportsCommands')
                ->with(array('MULTI', 'EXEC', 'DISCARD'))
                ->will($this->returnValue(true));
        $profile->expects($this->at(1))
                ->method('supportsCommands')
                ->with(array('WATCH', 'UNWATCH'))
                ->will($this->returnValue(false));

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $client = new Client($connection, array('profile' => $profile));

        $tx = new MultiExecContext($client, array('options' => 'cas'));
        $tx->watch('foo');
    }

    /**
     * @group disconnected
     */
    public function testExecutionWithFluentInterface()
    {
        $commands = array();
        $expected = array('one', 'two', 'three');

        $callback = $this->getExecuteCallback($expected, $commands);
        $tx = $this->getMockedTransaction($callback);

        $this->assertSame($expected, $tx->echo('one')->echo('two')->echo('three')->execute());
        $this->assertSame(array('MULTI', 'ECHO', 'ECHO', 'ECHO', 'EXEC'), self::commandsToIDs($commands));
    }

    /**
     * @group disconnected
     */
    public function testExecutionWithCallable()
    {
        $commands = array();
        $expected = array('one', 'two', 'three');

        $callback = $this->getExecuteCallback($expected, $commands);
        $tx = $this->getMockedTransaction($callback);

        $replies = $tx->execute(function ($tx) {
            $tx->echo('one');
            $tx->echo('two');
            $tx->echo('three');
        });

        $this->assertSame($expected, $replies);
        $this->assertSame(array('MULTI', 'ECHO', 'ECHO', 'ECHO', 'EXEC'), self::commandsToIDs($commands));
    }

    /**
     * @group disconnected
     */
    public function testCannotMixExecutionWithFluentInterfaceAndCallable()
    {
        $exception = null;

        $commands = array();

        $callback = $this->getExecuteCallback(null, $commands);
        $tx = $this->getMockedTransaction($callback);

        $exception = null;

        try {
            $tx->echo('foo')->execute(function ($tx) {
                $tx->echo('bar');
            });
        } catch (\Exception $exception) {
            // NOOP
        }

        $this->assertInstanceOf('Predis\ClientException', $exception);
        $this->assertSame(array('MULTI', 'ECHO', 'DISCARD'), self::commandsToIDs($commands));
    }

    /**
     * @group disconnected
     */
    public function testEmptyTransactionDoesNotSendMultiExecCommands()
    {
        $commands = array();

        $callback = $this->getExecuteCallback(null, $commands);
        $tx = $this->getMockedTransaction($callback);

        $replies = $tx->execute(function ($tx) {
            // NOOP
        });

        $this->assertNull($replies);
        $this->assertSame(array(), self::commandsToIDs($commands));
    }

    /**
     * @group disconnected
     * @expectedException Predis\ClientException
     * @expectedExceptionMessage Cannot invoke 'execute' or 'exec' inside an active client transaction block
     */
    public function testThrowsExceptionOnExecInsideTransactionBlock()
    {
        $commands = array();

        $callback = $this->getExecuteCallback(null, $commands);
        $tx = $this->getMockedTransaction($callback);

        $replies = $tx->execute(function ($tx) {
            $tx->exec();
        });

        $this->assertNull($replies);
        $this->assertSame(array(), self::commandsToIDs($commands));
    }

    /**
     * @group disconnected
     */
    public function testEmptyTransactionIgnoresDiscard()
    {
        $commands = array();

        $callback = $this->getExecuteCallback(null, $commands);
        $tx = $this->getMockedTransaction($callback);

        $replies = $tx->execute(function ($tx) {
            $tx->discard();
        });

        $this->assertNull($replies);
        $this->assertSame(array(), self::commandsToIDs($commands));
    }

    /**
     * @group disconnected
     */
    public function testTransactionWithCommandsSendsDiscard()
    {
        $commands = array();

        $callback = $this->getExecuteCallback(null, $commands);
        $tx = $this->getMockedTransaction($callback);

        $replies = $tx->execute(function ($tx) {
            $tx->set('foo', 'bar');
            $tx->get('foo');
            $tx->discard();
        });

        $this->assertNull($replies);
        $this->assertSame(array('MULTI', 'SET', 'GET', 'DISCARD'), self::commandsToIDs($commands));
    }

    /**
     * @group disconnected
     */
    public function testSendMultiOnCommandsFollowingDiscard()
    {
        $commands = array();
        $expected = array('after DISCARD');

        $callback = $this->getExecuteCallback($expected, $commands);
        $tx = $this->getMockedTransaction($callback);

        $replies = $tx->execute(function ($tx) {
            $tx->echo('before DISCARD');
            $tx->discard();
            $tx->echo('after DISCARD');
        });

        $this->assertSame($replies, $expected);
        $this->assertSame(array('MULTI', 'ECHO', 'DISCARD', 'MULTI', 'ECHO', 'EXEC'), self::commandsToIDs($commands));
    }
    /**
     * @group disconnected
     * @expectedException Predis\ClientException
     */
    public function testThrowsExceptionOnWatchInsideMulti()
    {
        $callback = $this->getExecuteCallback();
        $tx = $this->getMockedTransaction($callback);

        $tx->echo('foobar')->watch('foo')->execute();
    }

    /**
     * @group disconnected
     */
    public function testUnwatchInsideMulti()
    {
        $commands = array();
        $expected = array('foobar', true);

        $callback = $this->getExecuteCallback($expected, $commands);
        $tx = $this->getMockedTransaction($callback);

        $replies = $tx->echo('foobar')->unwatch('foo')->execute();

        $this->assertSame($replies, $expected);
        $this->assertSame(array('MULTI', 'ECHO', 'UNWATCH', 'EXEC'), self::commandsToIDs($commands));
    }

    /**
     * @group disconnected
     */
    public function testAutomaticWatchInOptions()
    {
        $txCommands = $casCommands = array();
        $expected = array('bar', 'piyo');
        $options = array('watch' => array('foo', 'hoge'));

        $callback = $this->getExecuteCallback($expected, $txCommands, $casCommands);
        $tx = $this->getMockedTransaction($callback, $options);

        $replies = $tx->execute(function ($tx) {
            $tx->get('foo');
            $tx->get('hoge');
        });

        $this->assertSame($replies, $expected);
        $this->assertSame(array('WATCH'), self::commandsToIDs($casCommands));
        $this->assertSame(array('foo', 'hoge'), $casCommands[0]->getArguments());
        $this->assertSame(array('MULTI', 'GET', 'GET', 'EXEC'), self::commandsToIDs($txCommands));
    }
    /**
     * @group disconnected
     */
    public function testCheckAndSetWithFluentInterface()
    {
        $txCommands = $casCommands = array();
        $expected = array('bar', 'piyo');
        $options = array('cas' => true, 'watch' => array('foo', 'hoge'));

        $callback = $this->getExecuteCallback($expected, $txCommands, $casCommands);
        $tx = $this->getMockedTransaction($callback, $options);

        $tx->watch('foobar');
        $this->assertSame('DUMMY_REPLY', $tx->get('foo'));
        $this->assertSame('DUMMY_REPLY', $tx->get('hoge'));

        $replies = $tx->multi()
                      ->get('foo')
                      ->get('hoge')
                      ->execute();

        $this->assertSame($replies, $expected);
        $this->assertSame(array('WATCH', 'WATCH', 'GET', 'GET'), self::commandsToIDs($casCommands));
        $this->assertSame(array('MULTI', 'GET', 'GET', 'EXEC'), self::commandsToIDs($txCommands));
    }

    /**
     * @group disconnected
     */
    public function testCheckAndSetWithBlock()
    {
        $txCommands = $casCommands = array();
        $expected = array('bar', 'piyo');
        $options = array('cas' => true, 'watch' => array('foo', 'hoge'));

        $callback = $this->getExecuteCallback($expected, $txCommands, $casCommands);
        $tx = $this->getMockedTransaction($callback, $options);

        $test = $this;
        $replies = $tx->execute(function ($tx) use ($test) {
            $tx->watch('foobar');

            $reply1 = $tx->get('foo');
            $reply2 = $tx->get('hoge');

            $test->assertSame('DUMMY_REPLY', $reply1);
            $test->assertSame('DUMMY_REPLY', $reply2);

            $tx->multi();

            $tx->get('foo');
            $tx->get('hoge');
        });

        $this->assertSame($replies, $expected);
        $this->assertSame(array('WATCH', 'WATCH', 'GET', 'GET'), self::commandsToIDs($casCommands));
        $this->assertSame(array('MULTI', 'GET', 'GET', 'EXEC'), self::commandsToIDs($txCommands));
    }

    /**
     * @group disconnected
     */
    public function testCheckAndSetWithEmptyBlock()
    {
        $txCommands = $casCommands = array();
        $options = array('cas' => true);

        $callback = $this->getExecuteCallback(array(), $txCommands, $casCommands);
        $tx = $this->getMockedTransaction($callback, $options);

        $tx->execute(function ($tx) {
            $tx->multi();
        });

        $this->assertSame(array(), self::commandsToIDs($casCommands));
        $this->assertSame(array(), self::commandsToIDs($txCommands));
    }

    /**
     * @group disconnected
     */
    public function testCheckAndSetWithoutExec()
    {
        $txCommands = $casCommands = array();
        $options = array('cas' => true);

        $callback = $this->getExecuteCallback(array(), $txCommands, $casCommands);
        $tx = $this->getMockedTransaction($callback, $options);

        $tx->execute(function ($tx) {
            $bar = $tx->get('foo');
            $tx->set('hoge', 'piyo');
        });

        $this->assertSame(array('GET', 'SET'), self::commandsToIDs($casCommands));
        $this->assertSame(array(), self::commandsToIDs($txCommands));
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Automatic retries can be used only when a transaction block is provided
     */
    public function testThrowsExceptionOnAutomaticRetriesWithFluentInterface()
    {
        $options = array('retry' => 1);

        $callback = $this->getExecuteCallback();
        $tx = $this->getMockedTransaction($callback, $options);

        $tx->echo('message')->execute();
    }

    /**
     * @group disconnected
     */
    public function testAutomaticRetryOnServerSideTransactionAbort()
    {
        $casCommands = $txCommands = array();
        $expected = array('bar');
        $options = array('watch' => array('foo', 'bar'), 'retry' => ($attempts = 2) + 1);

        $sentinel = $this->getMock('stdClass', array('signal'));
        $sentinel->expects($this->exactly($attempts))->method('signal');

        $callback = $this->getExecuteCallback($expected, $txCommands, $casCommands);
        $tx = $this->getMockedTransaction($callback, $options);

        $replies = $tx->execute(function ($tx) use ($sentinel, &$attempts) {
            $tx->get('foo');

            if ($attempts > 0) {
                $attempts -= 1;
                $sentinel->signal();

                $tx->echo('!!ABORT!!');
            }
        });

        $this->assertSame($replies, $expected);
        $this->assertSame(array('WATCH'), self::commandsToIDs($casCommands));
        $this->assertSame(array('foo', 'bar'), $casCommands[0]->getArguments());
        $this->assertSame(array('MULTI', 'GET', 'EXEC'), self::commandsToIDs($txCommands));
    }

    /**
     * @group disconnected
     * @expectedException Predis\Transaction\AbortedMultiExecException
     */
    public function testThrowsExceptionOnServerSideTransactionAbort()
    {
        $callback = $this->getExecuteCallback();
        $tx = $this->getMockedTransaction($callback);

        $replies = $tx->execute(function ($tx) {
            $tx->echo('!!ABORT!!');
        });
    }

    /**
     * @group disconnected
     */
    public function testHandlesStandardExceptionsInBlock()
    {
        $exception = null;

        $commands = array();
        $expected = array('foobar', true);

        $callback = $this->getExecuteCallback($expected, $commands);
        $tx = $this->getMockedTransaction($callback);

        $replies = null;

        try {
            $replies = $tx->execute(function ($tx) {
                $tx->set('foo', 'bar');
                $tx->get('foo');

                throw new \RuntimeException('TEST');
            });
        } catch (\Exception $exception) {
            // NOOP
        }

        $this->assertNull($replies, $expected);
        $this->assertSame(array('MULTI', 'SET', 'GET', 'DISCARD'), self::commandsToIDs($commands));
    }

    /**
     * @group disconnected
     */
    public function testHandlesServerExceptionsInBlock()
    {
        $commands = array();
        $expected = array('foobar', true);

        $callback = $this->getExecuteCallback($expected, $commands);
        $tx = $this->getMockedTransaction($callback);

        $replies = null;

        try {
            $replies = $tx->execute(function ($tx) {
                $tx->set('foo', 'bar');
                $tx->echo('ERR Invalid operation');
                $tx->get('foo');
            });
        } catch (ServerException $exception) {
            $tx->discard();
        }

        $this->assertNull($replies);
        $this->assertSame(array('MULTI', 'SET', 'ECHO', 'DISCARD'), self::commandsToIDs($commands));
    }

    /**
     * @group disconnected
     */
    public function testProperlyDiscardsTransactionAfterServerExceptionInBlock()
    {
        $connection = $this->getMockedConnection(function (CommandInterface $command) {
            switch ($command->getId()) {
                case 'MULTI':
                    return true;

                case 'ECHO':
                    return new ResponseError('ERR simulated failure on ECHO');

                case 'EXEC':
                    return new ResponseError('EXECABORT Transaction discarded because of previous errors.');

                default:
                    return new ResponseQueued();
            }
        });

        $client = new Client($connection);

        // First attempt
        $tx = new MultiExecContext($client);

        try {
            $tx->multi()->set('foo', 'bar')->echo('simulated failure')->exec();
        } catch (\Exception $exception) {
            $this->assertInstanceOf('Predis\Transaction\AbortedMultiExecException', $exception);
            $this->assertSame('ERR simulated failure on ECHO', $exception->getMessage());
        }

        // Second attempt
        $tx = new MultiExecContext($client);

        try {
            $tx->multi()->set('foo', 'bar')->echo('simulated failure')->exec();
        } catch (\Exception $exception) {
            $this->assertInstanceOf('Predis\Transaction\AbortedMultiExecException', $exception);
            $this->assertSame('ERR simulated failure on ECHO', $exception->getMessage());
        }
    }

    // ******************************************************************** //
    // ---- INTEGRATION TESTS --------------------------------------------- //
    // ******************************************************************** //

    /**
     * @group connected
     */
    public function testIntegrationHandlesStandardExceptionsInBlock()
    {
        $exception = null;

        $client = $this->getClient();

        try {
            $client->multiExec(function ($tx) {
                $tx->set('foo', 'bar');
                throw new \RuntimeException("TEST");
            });
        } catch (\Exception $exception) {
            // NOOP
        }

        $this->assertInstanceOf('RuntimeException', $exception);
        $this->assertFalse($client->exists('foo'));
    }

    /**
     * @group connected
     */
    public function testIntegrationThrowsExceptionOnRedisErrorInBlock()
    {
        $exception = null;

        $client = $this->getClient();
        $value = (string) rand();

        try {
            $client->multiExec(function ($tx) use ($value) {
                $tx->set('foo', 'bar');
                $tx->lpush('foo', 'bar');
                $tx->set('foo', $value);
            });
        } catch (ServerException $exception) {
            // NOOP
        }

        $this->assertInstanceOf('Predis\ResponseErrorInterface', $exception);
        $this->assertSame($value, $client->get('foo'));
    }

    /**
     * @group connected
     */
    public function testIntegrationReturnsErrorObjectOnRedisErrorInBlock()
    {
        $client = $this->getClient(array(), array('exceptions' => false));

        $replies = $client->multiExec(function ($tx) {
            $tx->set('foo', 'bar');
            $tx->lpush('foo', 'bar');
            $tx->echo('foobar');
        });

        $this->assertTrue($replies[0]);
        $this->assertInstanceOf('Predis\ResponseErrorInterface', $replies[1]);
        $this->assertSame('foobar', $replies[2]);
    }

    /**
     * @group connected
     */
    public function testIntegrationSendMultiOnCommandsAfterDiscard()
    {
        $client = $this->getClient();

        $replies = $client->multiExec(function ($tx) {
            $tx->set('foo', 'bar');
            $tx->discard();
            $tx->set('hoge', 'piyo');
        });

        $this->assertSame(1, count($replies));
        $this->assertFalse($client->exists('foo'));
        $this->assertTrue($client->exists('hoge'));
    }

    /**
     * @group connected
     */
    public function testIntegrationWritesOnWatchedKeysAbortTransaction()
    {
        $exception = null;

        $client1 = $this->getClient();
        $client2 = $this->getClient();

        try {
            $client1->multiExec(array('watch' => 'sentinel'), function ($tx) use ($client2) {
                $tx->set('sentinel', 'client1');
                $tx->get('sentinel');
                $client2->set('sentinel', 'client2');
            });
        } catch (AbortedMultiExecException $exception) {
            // NOOP
        }

        $this->assertInstanceOf('Predis\Transaction\AbortedMultiExecException', $exception);
        $this->assertSame('client2', $client1->get('sentinel'));
    }

    /**
     * @group connected
     */
    public function testIntegrationCheckAndSetWithDiscardAndRetry()
    {
        $client = $this->getClient();

        $client->set('foo', 'bar');
        $options = array('watch' => 'foo', 'cas' => true);

        $replies = $client->multiExec($options, function ($tx) {
            $tx->watch('foobar');
            $foo = $tx->get('foo');

            $tx->multi();
            $tx->set('foobar', $foo);
            $tx->discard();
            $tx->mget('foo', 'foobar');
        });

        $this->assertInternalType('array', $replies);
        $this->assertSame(array(array('bar', null)), $replies);

        $hijack = true;
        $client2 = $this->getClient();
        $client->set('foo', 'bar');

        $options = array('watch' => 'foo', 'cas' => true, 'retry' => 1);
        $replies = $client->multiExec($options, function ($tx) use ($client2, &$hijack) {
            $foo = $tx->get('foo');
            $tx->multi();

            $tx->set('foobar', $foo);
            $tx->discard();

            if ($hijack) {
                $hijack = false;
                $client2->set('foo', 'hijacked!');
            }

            $tx->mget('foo', 'foobar');
        });

        $this->assertInternalType('array', $replies);
        $this->assertSame(array(array('hijacked!', null)), $replies);
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Returns a mocked instance of Predis\Connection\SingleConnectionInterface
     * usingthe specified callback to return values from executeCommand().
     *
     * @param  \Closure                                     $executeCallback
     * @return \Predis\Connection\SingleConnectionInterface
     */
    protected function getMockedConnection($executeCallback)
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->any())
                   ->method('executeCommand')
                   ->will($this->returnCallback($executeCallback));

        return $connection;
    }

    /**
     * Returns a mocked instance of Predis\Transaction\MultiExecContext using
     * the specified callback to return values from the executeCommand method
     * of the underlying connection.
     *
     * @param  \Closure         $executeCallback
     * @param  array            $options
     * @return MultiExecContext
     */
    protected function getMockedTransaction($executeCallback, $options = array())
    {
        $connection = $this->getMockedConnection($executeCallback);
        $client = new Client($connection);
        $transaction = new MultiExecContext($client, $options);

        return $transaction;
    }

    /**
     * Returns a callback that emulates a server-side MULTI/EXEC transaction context.
     *
     * @param  array    $expected Expected responses.
     * @param  array    $commands Reference to an array storing the whole flow of commands.
     * @param  array    $cas      Check and set operations performed by the transaction.
     * @return \Closure
     */
    protected function getExecuteCallback($expected = array(), &$commands = array(), &$cas = array())
    {
        $multi = $watch = $abort = false;

        return function (CommandInterface $command) use (&$expected, &$commands, &$cas, &$multi, &$watch, &$abort) {
            $cmd = $command->getId();

            if ($multi || $cmd === 'MULTI') {
                $commands[] = $command;
            } else {
                $cas[] = $command;
            }

            switch ($cmd) {
                case 'WATCH':
                    if ($multi) {
                        throw new ServerException("ERR $cmd inside MULTI is not allowed");
                    }

                    return $watch = true;

                case 'MULTI':
                    if ($multi) {
                        throw new ServerException("ERR MULTI calls can not be nested");
                    }

                    return $multi = true;

                case 'EXEC':
                    if (!$multi) {
                        throw new ServerException("ERR $cmd without MULTI");
                    }

                    $watch = $multi = false;

                    if ($abort) {
                        $commands = $cas = array();
                        $abort = false;

                        return null;
                    }

                    return $expected;

                case 'DISCARD':
                    if (!$multi) {
                        throw new ServerException("ERR $cmd without MULTI");
                    }

                    $watch = $multi = false;

                    return true;

                case 'ECHO':
                    @list($trigger) = $command->getArguments();
                    if (strpos($trigger, 'ERR ') === 0) {
                        throw new ServerException($trigger);
                    }

                    if ($trigger === '!!ABORT!!' && $multi) {
                        $abort = true;
                    }

                    return new ResponseQueued();

                case 'UNWATCH':
                    $watch = false;

                default:
                    return $multi ? new ResponseQueued() : 'DUMMY_REPLY';
            }
        };
    }

    /**
     * Converts an array of instances of Predis\Command\CommandInterface and
     * returns an array containing their IDs.
     *
     * @param  array $commands List of commands instances.
     * @return array
     */
    protected static function commandsToIDs($commands)
    {
        return array_map(function ($cmd) { return $cmd->getId(); }, $commands);
    }

    /**
     * Returns a client instance connected to the specified Redis
     * server instance to perform integration tests.
     *
     * @param array Additional connection parameters.
     * @param array Additional client options.
     * @return Client client instance.
     */
    protected function getClient(array $parameters = array(), array $options = array())
    {
        return $this->createClient($parameters, $options);
    }
}
