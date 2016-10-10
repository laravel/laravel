<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis;

use PredisTestCase;
use Predis\Connection\ConnectionFactory;
use Predis\Connection\MasterSlaveReplication;
use Predis\Connection\PredisCluster;
use Predis\Profile\ServerProfile;

/**
 *
 */
class ClientTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testConstructorWithoutArguments()
    {
        $client = new Client();

        $connection = $client->getConnection();
        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $connection);

        $parameters = $connection->getParameters();
        $this->assertSame($parameters->host, '127.0.0.1');
        $this->assertSame($parameters->port, 6379);

        $options = $client->getOptions();
        $this->assertSame($options->profile->getVersion(), ServerProfile::getDefault()->getVersion());

        $this->assertFalse($client->isConnected());
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithNullArgument()
    {
        $client = new Client(null);

        $connection = $client->getConnection();
        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $connection);

        $parameters = $connection->getParameters();
        $this->assertSame($parameters->host, '127.0.0.1');
        $this->assertSame($parameters->port, 6379);

        $options = $client->getOptions();
        $this->assertSame($options->profile->getVersion(), ServerProfile::getDefault()->getVersion());

        $this->assertFalse($client->isConnected());
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithNullAndNullArguments()
    {
        $client = new Client(null, null);

        $connection = $client->getConnection();
        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $connection);

        $parameters = $connection->getParameters();
        $this->assertSame($parameters->host, '127.0.0.1');
        $this->assertSame($parameters->port, 6379);

        $options = $client->getOptions();
        $this->assertSame($options->profile->getVersion(), ServerProfile::getDefault()->getVersion());

        $this->assertFalse($client->isConnected());
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithArrayArgument()
    {
        $client = new Client($arg1 = array('host' => 'localhost', 'port' => 7000));

        $parameters = $client->getConnection()->getParameters();
        $this->assertSame($parameters->host, $arg1['host']);
        $this->assertSame($parameters->port, $arg1['port']);
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithArrayOfArrayArgument()
    {
        $arg1 = array(
            array('host' => 'localhost', 'port' => 7000),
            array('host' => 'localhost', 'port' => 7001),
        );

        $client = new Client($arg1);

        $this->assertInstanceOf('Predis\Connection\ClusterConnectionInterface', $client->getConnection());
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithStringArgument()
    {
        $client = new Client('tcp://localhost:7000');

        $parameters = $client->getConnection()->getParameters();
        $this->assertSame($parameters->host, 'localhost');
        $this->assertSame($parameters->port, 7000);
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithArrayOfStringArgument()
    {
        $client = new Client($arg1 = array('tcp://localhost:7000', 'tcp://localhost:7001'));

        $this->assertInstanceOf('Predis\Connection\ClusterConnectionInterface', $client->getConnection());
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithArrayOfConnectionsArgument()
    {
        $connection1 = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection2 = $this->getMock('Predis\Connection\SingleConnectionInterface');

        $client = new Client(array($connection1, $connection2));

        $this->assertInstanceOf('Predis\Connection\ClusterConnectionInterface', $cluster = $client->getConnection());
        $this->assertSame($connection1, $cluster->getConnectionById(0));
        $this->assertSame($connection2, $cluster->getConnectionById(1));
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithConnectionArgument()
    {
        $factory = new ConnectionFactory();
        $connection = $factory->create('tcp://localhost:7000');

        $client = new Client($connection);

        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $client->getConnection());
        $this->assertSame($connection, $client->getConnection());

        $parameters = $client->getConnection()->getParameters();
        $this->assertSame($parameters->host, 'localhost');
        $this->assertSame($parameters->port, 7000);
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithClusterArgument()
    {
        $cluster = new PredisCluster();

        $factory = new ConnectionFactory();
        $factory->createAggregated($cluster, array('tcp://localhost:7000', 'tcp://localhost:7001'));

        $client = new Client($cluster);

        $this->assertInstanceOf('Predis\Connection\ClusterConnectionInterface', $client->getConnection());
        $this->assertSame($cluster, $client->getConnection());
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithReplicationArgument()
    {
        $replication = new MasterSlaveReplication();

        $factory = new ConnectionFactory();
        $factory->createAggregated($replication, array('tcp://host1?alias=master', 'tcp://host2?alias=slave'));

        $client = new Client($replication);

        $this->assertInstanceOf('Predis\Connection\ReplicationConnectionInterface', $client->getConnection());
        $this->assertSame($replication, $client->getConnection());
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithCallableArgument()
    {
        $connection = $this->getMock('Predis\Connection\ConnectionInterface');

        $callable = $this->getMock('stdClass', array('__invoke'));
        $callable->expects($this->once())
                 ->method('__invoke')
                 ->with($this->isInstanceOf('Predis\Option\ClientOptions'))
                 ->will($this->returnValue($connection));

        $client = new Client($callable);

        $this->assertSame($connection, $client->getConnection());
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Callable parameters must return instances of Predis\Connection\ConnectionInterface
     */
    public function testConstructorWithCallableArgumentButInvalidReturnType()
    {
        $wrongType = $this->getMock('stdClass');

        $callable = $this->getMock('stdClass', array('__invoke'));
        $callable->expects($this->once())
                 ->method('__invoke')
                 ->with($this->isInstanceOf('Predis\Option\ClientOptions'))
                 ->will($this->returnValue($wrongType));

        $client = new Client($callable);
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithNullAndArrayArgument()
    {
        $factory = $this->getMock('Predis\Connection\ConnectionFactoryInterface');

        $arg2 = array('profile' => '2.0', 'prefix' => 'prefix:', 'connections' => $factory);
        $client = new Client(null, $arg2);

        $profile = $client->getProfile();
        $this->assertSame($profile->getVersion(), ServerProfile::get('2.0')->getVersion());
        $this->assertInstanceOf('Predis\Command\Processor\KeyPrefixProcessor', $profile->getProcessor());
        $this->assertSame('prefix:', $profile->getProcessor()->getPrefix());

        $this->assertSame($factory, $client->getConnectionFactory());
    }

    /**
     * @group disconnected
     */
    public function testConstructorWithArrayAndOptionReplicationArgument()
    {
        $arg1 = array('tcp://host1?alias=master', 'tcp://host2?alias=slave');
        $arg2 = array('replication' => true);
        $client = new Client($arg1, $arg2);

        $this->assertInstanceOf('Predis\Connection\ReplicationConnectionInterface', $connection = $client->getConnection());
        $this->assertSame('host1', $connection->getConnectionById('master')->getParameters()->host);
        $this->assertSame('host2', $connection->getConnectionById('slave')->getParameters()->host);
    }

    /**
     * @group disconnected
     */
    public function testConnectAndDisconnect()
    {
        $connection = $this->getMock('Predis\Connection\ConnectionInterface');
        $connection->expects($this->once())->method('connect');
        $connection->expects($this->once())->method('disconnect');

        $client = new Client($connection);
        $client->connect();
        $client->disconnect();
    }

    /**
     * @group disconnected
     */
    public function testIsConnectedChecksConnectionState()
    {
        $connection = $this->getMock('Predis\Connection\ConnectionInterface');
        $connection->expects($this->once())->method('isConnected');

        $client = new Client($connection);
        $client->isConnected();
    }

    /**
     * @group disconnected
     */
    public function testQuitIsAliasForDisconnect()
    {
        $connection = $this->getMock('Predis\Connection\ConnectionInterface');
        $connection->expects($this->once())->method('disconnect');

        $client = new Client($connection);
        $client->quit();
    }

    /**
     * @group disconnected
     */
    public function testCreatesNewCommandUsingSpecifiedProfile()
    {
        $ping = ServerProfile::getDefault()->createCommand('ping', array());

        $profile = $this->getMock('Predis\Profile\ServerProfileInterface');
        $profile->expects($this->once())
                ->method('createCommand')
                ->with('ping', array())
                ->will($this->returnValue($ping));

        $client = new Client(null, array('profile' => $profile));
        $this->assertSame($ping, $client->createCommand('ping', array()));
    }

    /**
     * @group disconnected
     */
    public function testExecuteCommandReturnsParsedReplies()
    {
        $profile = ServerProfile::getDefault();

        $ping = $profile->createCommand('ping', array());
        $hgetall = $profile->createCommand('hgetall', array('metavars', 'foo', 'hoge'));

        $connection= $this->getMock('Predis\Connection\ConnectionInterface');
        $connection->expects($this->at(0))
                   ->method('executeCommand')
                   ->with($ping)
                   ->will($this->returnValue('PONG'));
        $connection->expects($this->at(1))
                   ->method('executeCommand')
                   ->with($hgetall)
                   ->will($this->returnValue(array('foo', 'bar', 'hoge', 'piyo')));

        $client = new Client($connection);

        $this->assertTrue($client->executeCommand($ping));
        $this->assertSame(array('foo' => 'bar', 'hoge' => 'piyo'), $client->executeCommand($hgetall));
    }

    /**
     * @group disconnected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testExecuteCommandThrowsExceptionOnRedisError()
    {
        $ping = ServerProfile::getDefault()->createCommand('ping', array());
        $expectedResponse = new ResponseError('ERR Operation against a key holding the wrong kind of value');

        $connection= $this->getMock('Predis\Connection\ConnectionInterface');
        $connection->expects($this->once())
                   ->method('executeCommand')
                   ->will($this->returnValue($expectedResponse));

        $client = new Client($connection);
        $client->executeCommand($ping);
    }

    /**
     * @group disconnected
     */
    public function testExecuteCommandReturnsErrorResponseOnRedisError()
    {
        $ping = ServerProfile::getDefault()->createCommand('ping', array());
        $expectedResponse = new ResponseError('ERR Operation against a key holding the wrong kind of value');

        $connection= $this->getMock('Predis\Connection\ConnectionInterface');
        $connection->expects($this->once())
                   ->method('executeCommand')
                   ->will($this->returnValue($expectedResponse));

        $client = new Client($connection, array('exceptions' => false));
        $response = $client->executeCommand($ping);

        $this->assertSame($response, $expectedResponse);
    }

    /**
     * @group disconnected
     */
    public function testCallingRedisCommandExecutesInstanceOfCommand()
    {
        $ping = ServerProfile::getDefault()->createCommand('ping', array());

        $connection = $this->getMock('Predis\Connection\ConnectionInterface');
        $connection->expects($this->once())
                   ->method('executeCommand')
                   ->with($this->isInstanceOf('Predis\Command\ConnectionPing'))
                   ->will($this->returnValue('PONG'));

        $profile = $this->getMock('Predis\Profile\ServerProfileInterface');
        $profile->expects($this->once())
                ->method('createCommand')
                ->with('ping', array())
                ->will($this->returnValue($ping));

        $options = array('profile' => $profile);
        $client = $this->getMock('Predis\Client', null, array($connection, $options));

        $this->assertTrue($client->ping());
    }

    /**
     * @group disconnected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testCallingRedisCommandThrowsExceptionOnServerError()
    {
        $expectedResponse = new ResponseError('ERR Operation against a key holding the wrong kind of value');

        $connection = $this->getMock('Predis\Connection\ConnectionInterface');
        $connection->expects($this->once())
                   ->method('executeCommand')
                   ->with($this->isInstanceOf('Predis\Command\ConnectionPing'))
                   ->will($this->returnValue($expectedResponse));

        $client = new Client($connection);
        $client->ping();
    }

    /**
     * @group disconnected
     */
    public function testCallingRedisCommandReturnsErrorResponseOnRedisError()
    {
        $expectedResponse = new ResponseError('ERR Operation against a key holding the wrong kind of value');

        $connection = $this->getMock('Predis\Connection\ConnectionInterface');
        $connection->expects($this->once())
                   ->method('executeCommand')
                   ->with($this->isInstanceOf('Predis\Command\ConnectionPing'))
                   ->will($this->returnValue($expectedResponse));

        $client = new Client($connection, array('exceptions' => false));
        $response = $client->ping();

        $this->assertSame($response, $expectedResponse);
    }

    /**
     * @group disconnected
     * @expectedException Predis\ClientException
     * @expectedExceptionMessage 'invalidcommand' is not a registered Redis command
     */
    public function testThrowsExceptionOnNonRegisteredRedisCommand()
    {
        $client = new Client();
        $client->invalidCommand();
    }

    /**
     * @group disconnected
     */
    public function testGetConnectionFromAggregatedConnectionWithAlias()
    {
        $client = new Client(array('tcp://host1?alias=node01', 'tcp://host2?alias=node02'));

        $this->assertInstanceOf('Predis\Connection\ClusterConnectionInterface', $cluster = $client->getConnection());
        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $node01 = $client->getConnectionById('node01'));
        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $node02 = $client->getConnectionById('node02'));

        $this->assertSame('host1', $node01->getParameters()->host);
        $this->assertSame('host2', $node02->getParameters()->host);
    }

    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage Retrieving connections by ID is supported only when using aggregated connections
     */
    public function testGetConnectionByIdWorksOnlyWithAggregatedConnections()
    {
        $client = new Client();

        $client->getConnectionById('node01');
    }

    /**
     * @group disconnected
     */
    public function testCreateClientWithConnectionFromAggregatedConnection()
    {
        $client = new Client(array('tcp://host1?alias=node01', 'tcp://host2?alias=node02'), array('prefix' => 'pfx:'));

        $this->assertInstanceOf('Predis\Connection\ClusterConnectionInterface', $cluster = $client->getConnection());
        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $node01 = $client->getConnectionById('node01'));
        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $node02 = $client->getConnectionById('node02'));

        $clientNode02 = $client->getClientFor('node02');

        $this->assertInstanceOf('Predis\Client', $clientNode02);
        $this->assertSame($node02, $clientNode02->getConnection());
        $this->assertSame($client->getOptions(), $clientNode02->getOptions());
    }

    /**
     * @group disconnected
     */
    public function testGetClientForReturnsInstanceOfSubclass()
    {
        $nodes = array('tcp://host1?alias=node01', 'tcp://host2?alias=node02');
        $client = $this->getMock('Predis\Client', array('dummy'), array($nodes), 'SubclassedClient');

        $this->assertInstanceOf('SubclassedClient', $client->getClientFor('node02'));
    }

    /**
     * @group disconnected
     */
    public function testPipelineWithoutArgumentsReturnsPipelineContext()
    {
        $client = new Client();

        $this->assertInstanceOf('Predis\Pipeline\PipelineContext', $client->pipeline());
    }

    /**
     * @group disconnected
     */
    public function testPipelineWithArrayReturnsPipelineContextWithOptions()
    {
        $client = new Client();
        $executor = $this->getMock('Predis\Pipeline\PipelineExecutorInterface');

        $options = array('executor' => $executor);
        $this->assertInstanceOf('Predis\Pipeline\PipelineContext', $pipeline = $client->pipeline($options));
        $this->assertSame($executor, $pipeline->getExecutor());

        $options = array('executor' => function ($client, $options) use ($executor) { return $executor; });
        $this->assertInstanceOf('Predis\Pipeline\PipelineContext', $pipeline = $client->pipeline($options));
        $this->assertSame($executor, $pipeline->getExecutor());
    }

    /**
     * @group disconnected
     */
    public function testPipelineWithCallableExecutesPipeline()
    {
        $callable = $this->getMock('stdClass', array('__invoke'));
        $callable->expects($this->once())
                 ->method('__invoke')
                 ->with($this->isInstanceOf('Predis\Pipeline\PipelineContext'));

        $client = new Client();
        $client->pipeline($callable);
    }

    /**
     * @group disconnected
     */
    public function testPipelineWithArrayAndCallableExecutesPipelineWithOptions()
    {
        $executor = $this->getMock('Predis\Pipeline\PipelineExecutorInterface');
        $options = array('executor' => $executor);

        $test = $this;
        $mockCallback = function ($pipeline) use ($executor, $test) {
            $reflection = new \ReflectionProperty($pipeline, 'executor');
            $reflection->setAccessible(true);

            $test->assertSame($executor, $reflection->getValue($pipeline));
        };

        $callable = $this->getMock('stdClass', array('__invoke'));
        $callable->expects($this->once())
                 ->method('__invoke')
                 ->with($this->isInstanceOf('Predis\Pipeline\PipelineContext'))
                 ->will($this->returnCallback($mockCallback));

        $client = new Client();
        $client->pipeline($options, $callable);
    }

    /**
     * @group disconnected
     */
    public function testPubSubLoopWithoutArgumentsReturnsPubSubContext()
    {
        $client = new Client();

        $this->assertInstanceOf('Predis\PubSub\PubSubContext', $client->pubSubLoop());
    }

    /**
     * @group disconnected
     */
    public function testPubSubLoopWithArrayReturnsPubSubContextWithOptions()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $options = array('subscribe' => 'channel');

        $client = new Client($connection);

        $this->assertInstanceOf('Predis\PubSub\PubSubContext', $pubsub = $client->pubSubLoop($options));

        $reflection = new \ReflectionProperty($pubsub, 'options');
        $reflection->setAccessible(true);

        $this->assertSame($options, $reflection->getValue($pubsub));
    }

    /**
     * @group disconnected
     */
    public function testPubSubLoopWithArrayAndCallableExecutesPubSub()
    {
        // NOTE: we use a subscribe count of 0 in the fake message to trick
        //       the context and to make it think that it can be closed
        //       since there are no more subscriptions active.

        $message = array('subscribe', 'channel', 0);
        $options = array('subscribe' => 'channel');

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())
                   ->method('read')
                   ->will($this->returnValue($message));

        $callable = $this->getMock('stdClass', array('__invoke'));
        $callable->expects($this->once())
                 ->method('__invoke');

        $client = new Client($connection);
        $client->pubSubLoop($options, $callable);
    }

    /**
     * @group disconnected
     */
    public function testPubSubIsAliasForPubSubLoop()
    {
        $client = new Client();

        $this->assertInstanceOf('Predis\PubSub\PubSubContext', $client->pubSub());
    }

    /**
     * @group disconnected
     */
    public function testMultiExecWithoutArgumentsReturnsMultiExecContext()
    {
        $client = new Client();

        $this->assertInstanceOf('Predis\Transaction\MultiExecContext', $client->multiExec());
    }

    /**
     * @group disconnected
     */
    public function testMethodTransactionIsAliasForMethodMultiExec()
    {
        $client = new Client();

        $this->assertInstanceOf('Predis\Transaction\MultiExecContext', $client->transaction());
    }

    /**
     * @group disconnected
     */
    public function testMultiExecWithArrayReturnsMultiExecContextWithOptions()
    {
        $options = array('cas' => true, 'retry' => 3);

        $client = new Client();

        $this->assertInstanceOf('Predis\Transaction\MultiExecContext', $tx = $client->multiExec($options));

        $reflection = new \ReflectionProperty($tx, 'options');
        $reflection->setAccessible(true);

        $this->assertSame($options, $reflection->getValue($tx));
    }

    /**
     * @group disconnected
     */
    public function testMultiExecWithArrayAndCallableExecutesMultiExec()
    {
        // NOTE: we use CAS since testing the actual MULTI/EXEC context
        //       here is not the point.
        $options = array('cas' => true, 'retry' => 3);

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->once())
                   ->method('executeCommand')
                   ->will($this->returnValue(new ResponseQueued()));

        $txCallback = function ($tx) {
            $tx->ping();
        };

        $callable = $this->getMock('stdClass', array('__invoke'));
        $callable->expects($this->once())
                 ->method('__invoke')
                 ->will($this->returnCallback($txCallback));

        $client = new Client($connection);
        $client->multiExec($options, $callable);
    }

    /**
     * @group disconnected
     */
    public function testMonitorReturnsMonitorContext()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $client = new Client($connection);

        $this->assertInstanceOf('Predis\Monitor\MonitorContext', $monitor = $client->monitor());
    }

    /**
     * @group disconnected
     */
    public function testClientResendScriptedCommandUsingEvalOnNoScriptErrors()
    {
        $command = $this->getMockForAbstractClass('Predis\Command\ScriptedCommand', array(), '', true, true, true, array('parseResponse'));
        $command->expects($this->once())
                ->method('getScript')
                ->will($this->returnValue('return redis.call(\'exists\', KEYS[1])'));
        $command->expects($this->once())
                ->method('parseResponse')
                ->with('OK')
                ->will($this->returnValue(true));

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->at(0))
                   ->method('executeCommand')
                   ->with($command)
                   ->will($this->returnValue(new ResponseError('NOSCRIPT')));
        $connection->expects($this->at(1))
                   ->method('executeCommand')
                   ->with($this->isInstanceOf('Predis\Command\ServerEval'))
                   ->will($this->returnValue('OK'));

        $client = new Client($connection);

        $this->assertTrue($client->executeCommand($command));
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Returns an URI string representation of the specified connection parameters.
     *
     * @param  Array  $parameters Array of connection parameters.
     * @return String URI string.
     */
    protected function getParametersString(Array $parameters)
    {
        $defaults = $this->getDefaultParametersArray();

        $scheme = isset($parameters['scheme']) ? $parameters['scheme'] : $defaults['scheme'];
        $host = isset($parameters['host']) ? $parameters['host'] : $defaults['host'];
        $port = isset($parameters['port']) ? $parameters['port'] : $defaults['port'];

        unset($parameters['scheme'], $parameters['host'], $parameters['port']);
        $uriString = "$scheme://$host:$port/?";

        foreach ($parameters as $k => $v) {
            $uriString .= "$k=$v&";
        }

        return $uriString;
    }
}
