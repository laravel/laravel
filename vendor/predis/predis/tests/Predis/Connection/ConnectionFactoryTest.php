<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Connection;

use PredisTestCase;
/**
 *
 */
class ConnectionFactoryTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testImplementsCorrectInterface()
    {
        $factory = new ConnectionFactory();

        $this->assertInstanceOf('Predis\Connection\ConnectionFactoryInterface', $factory);
    }

    /**
     * @group disconnected
     */
    public function testCreateConnection()
    {
        $factory = new ConnectionFactory();

        $tcp = new ConnectionParameters(array(
            'scheme' => 'tcp',
            'host' => 'locahost',
        ));

        $connection = $factory->create($tcp);
        $parameters = $connection->getParameters();
        $this->assertInstanceOf('Predis\Connection\StreamConnection', $connection);
        $this->assertEquals($tcp->scheme, $parameters->scheme);
        $this->assertEquals($tcp->host, $parameters->host);
        $this->assertEquals($tcp->database, $parameters->database);

        $unix = new ConnectionParameters(array(
            'scheme' => 'unix',
            'path' => '/tmp/redis.sock',
        ));

        $connection = $factory->create($tcp);
        $parameters = $connection->getParameters();
        $this->assertInstanceOf('Predis\Connection\StreamConnection', $connection);
        $this->assertEquals($tcp->scheme, $parameters->scheme);
        $this->assertEquals($tcp->database, $parameters->database);
    }

    /**
     * @group disconnected
     */
    public function testCreateConnectionWithNullParameters()
    {
        $factory = new ConnectionFactory();
        $connection = $factory->create(null);
        $parameters = $connection->getParameters();

        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $connection);
        $this->assertEquals('tcp', $parameters->scheme);

        $this->assertFalse(isset($parameters->custom));
        $this->assertNull($parameters->custom);
    }

    /**
     * @group disconnected
     */
    public function testCreateConnectionWithArrayParameters()
    {
        $factory = new ConnectionFactory();
        $connection = $factory->create(array('scheme' => 'tcp', 'custom' => 'foobar'));
        $parameters = $connection->getParameters();

        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $connection);
        $this->assertEquals('tcp', $parameters->scheme);

        $this->assertTrue(isset($parameters->custom));
        $this->assertSame('foobar', $parameters->custom);
    }

    /**
     * @group disconnected
     */
    public function testCreateConnectionWithStringURI()
    {
        $factory = new ConnectionFactory();
        $connection = $factory->create('tcp://127.0.0.1?custom=foobar');
        $parameters = $connection->getParameters();

        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $connection);
        $this->assertEquals('tcp', $parameters->scheme);

        $this->assertTrue(isset($parameters->custom));
        $this->assertSame('foobar', $parameters->custom);
    }

    /**
     * @group disconnected
     */
    public function testCreateConnectionWithoutInitializationCommands()
    {
        $profile = $this->getMock('Predis\Profile\ServerProfileInterface');
        $profile->expects($this->never())->method('create');

        $factory = new ConnectionFactory($profile);
        $parameters = new ConnectionParameters();
        $connection = $factory->create($parameters);

        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $connection);
    }

    /**
     * @group disconnected
     */
    public function testCreateConnectionWithInitializationCommands()
    {
        $test = $this;
        $database = 15;
        $password = 'foobar';
        $commands = array();

        $createCommand = function ($id, $arguments) use ($test, &$commands) {
            $commands[$id] = $arguments;
            $command = $test->getMock('Predis\Command\CommandInterface');

            return $command;
        };

        $profile = $this->getMock('Predis\Profile\ServerProfileInterface');
        $profile->expects($this->exactly(2))
                ->method('createCommand')
                ->with($this->isType('string'), $this->isType('array'))
                ->will($this->returnCallback($createCommand));

        $factory = new ConnectionFactory($profile);
        $parameters = new ConnectionParameters(array('database' => $database, 'password' => $password));
        $connection = $factory->create($parameters);

        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $connection);
        $this->assertEquals(2, count($commands));   // TODO: assertCount()?
        $this->assertEquals(array($database), $commands['select']);
        $this->assertEquals(array($password), $commands['auth']);
    }

    /**
     * @group disconnected
     * @todo This test smells but there's no other way around it right now.
     */
    public function testCreateConnectionWithDatabaseAndPasswordButNoProfile()
    {
        $parameters = new ConnectionParameters(array('database' => 0, 'password' => 'foobar'));

        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $connection->expects($this->never())
                   ->method('getParameters')
                   ->will($this->returnValue($parameters));
        $connection->expects($this->never())
                   ->method('pushInitCommand');

        $factory = new ConnectionFactory();

        $reflection = new \ReflectionObject($factory);
        $prepareConnection = $reflection->getMethod('prepareConnection');
        $prepareConnection->setAccessible(true);
        $prepareConnection->invoke($factory, $connection);
    }

    /**
     * @group disconnected
     */
    public function testCreateUndefinedConnection()
    {
        $scheme = 'unknown';
        $this->setExpectedException('InvalidArgumentException', "Unknown connection scheme: $scheme");

        $factory = new ConnectionFactory();
        $factory->create(new ConnectionParameters(array('scheme' => $scheme)));
    }

    /**
     * @group disconnected
     */
    public function testDefineConnectionWithFQN()
    {
        list(, $connectionClass) = $this->getMockConnectionClass();

        $parameters = new ConnectionParameters(array('scheme' => 'foobar'));
        $factory = new ConnectionFactory();

        $factory->define($parameters->scheme, $connectionClass);
        $connection = $factory->create($parameters);

        $this->assertInstanceOf($connectionClass, $connection);
    }

    /**
     * @group disconnected
     */
    public function testDefineConnectionWithCallable()
    {
        list(, $connectionClass) = $this->getMockConnectionClass();

        $parameters = new ConnectionParameters(array('scheme' => 'foobar'));

        $initializer = function ($parameters) use ($connectionClass) {
            return new $connectionClass($parameters);
        };

        $initializerMock = $this->getMock('stdClass', array('__invoke'));
        $initializerMock->expects($this->exactly(2))
                        ->method('__invoke')
                        ->with($parameters)
                        ->will($this->returnCallback($initializer));

        $factory = new ConnectionFactory();
        $factory->define($parameters->scheme, $initializerMock);
        $connection1 = $factory->create($parameters);
        $connection2 = $factory->create($parameters);

        $this->assertInstanceOf($connectionClass, $connection1);
        $this->assertInstanceOf($connectionClass, $connection2);
        $this->assertNotSame($connection1, $connection2);
    }

    /**
     * @group disconnected
     */
    public function testDefineConnectionWithInvalidArgument()
    {
        $this->setExpectedException('InvalidArgumentException');

        $factory = new ConnectionFactory();
        $factory->define('foobar', new \stdClass());
    }

    /**
     * @group disconnected
     */
    public function testUndefineDefinedConnection()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unknown connection scheme: tcp');

        $factory = new ConnectionFactory();
        $factory->undefine('tcp');
        $factory->create('tcp://127.0.0.1');
    }

    /**
     * @group disconnected
     */
    public function testUndefineUndefinedConnection()
    {
        $factory = new ConnectionFactory();
        $factory->undefine('unknown');
        $connection = $factory->create('tcp://127.0.0.1');

        $this->assertInstanceOf('Predis\Connection\SingleConnectionInterface', $connection);
    }

    /**
     * @group disconnected
     */
    public function testDefineAndUndefineConnection()
    {
        list(, $connectionClass) = $this->getMockConnectionClass();

        $factory = new ConnectionFactory();

        $factory->define('redis', $connectionClass);
        $this->assertInstanceOf($connectionClass, $factory->create('redis://127.0.0.1'));

        $factory->undefine('redis');
        $this->setExpectedException('InvalidArgumentException', 'Unknown connection scheme: redis');
        $factory->create('redis://127.0.0.1');
    }

    /**
     * @group disconnected
     */
    public function testAggregatedConnectionSkipCreationOnConnectionInstance()
    {
        list(, $connectionClass) = $this->getMockConnectionClass();

        $cluster = $this->getMock('Predis\Connection\ClusterConnectionInterface');
        $cluster->expects($this->exactly(2))
                ->method('add')
                ->with($this->isInstanceOf('Predis\Connection\SingleConnectionInterface'));

        $factory = $this->getMock('Predis\Connection\ConnectionFactory', array('create'));
        $factory->expects($this->never())
                ->method('create');

        $factory->createAggregated($cluster, array(new $connectionClass(), new $connectionClass()));
    }

    /**
     * @group disconnected
     */
    public function testAggregatedConnectionWithMixedParameters()
    {
        list(, $connectionClass) = $this->getMockConnectionClass();

        $cluster = $this->getMock('Predis\Connection\ClusterConnectionInterface');
        $cluster->expects($this->exactly(4))
                ->method('add')
                ->with($this->isInstanceOf('Predis\Connection\SingleConnectionInterface'));

        $factory = $this->getMock('Predis\Connection\ConnectionFactory', array('create'));
        $factory->expects($this->exactly(3))
                ->method('create')
                ->will($this->returnCallback(function ($_) use ($connectionClass) {
                    return new $connectionClass;
                }));

        $factory->createAggregated($cluster, array(null, 'tcp://127.0.0.1', array('scheme' => 'tcp'), new $connectionClass()));
    }

    /**
     * @group disconnected
     */
    public function testAggregatedConnectionWithEmptyListOfParameters()
    {
        $cluster = $this->getMock('Predis\Connection\ClusterConnectionInterface');
        $cluster->expects($this->never())->method('add');

        $factory = $this->getMock('Predis\Connection\ConnectionFactory', array('create'));
        $factory->expects($this->never())->method('create');

        $factory->createAggregated($cluster, array());
    }

    /**
     * @group disconnected
     * @todo We might want to add a test for SingleConnectionInterface::pushInitCommand().
     */
    public function testAggregatedConnectionWithServerProfileArgument()
    {
        list(, $connectionClass) = $this->getMockConnectionClass();

        $cluster = $this->getMock('Predis\Connection\ClusterConnectionInterface');
        $profile = $this->getMock('Predis\Profile\ServerProfileInterface');

        $factory = $this->getMock('Predis\Connection\ConnectionFactory', array('create'), array($profile));
        $factory->expects($this->exactly(2))
                ->method('create')
                ->with($this->anything())
                ->will($this->returnCallback(function ($_) use ($connectionClass) {
                    return new $connectionClass();
                }));

        $nodes = array('tcp://127.0.0.1:7001?password=foo', 'tcp://127.0.0.1:7002?password=bar');
        $factory->createAggregated($cluster, $nodes);
    }


    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Returns a mocked Predis\Connection\SingleConnectionInterface.
     *
     * @return Array Mock instance and class name
     */
    protected function getMockConnectionClass()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');

        return array($connection, get_class($connection));
    }
}
