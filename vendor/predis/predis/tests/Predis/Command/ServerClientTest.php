<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command;

/**
 * @group commands
 * @group realm-server
 */
class ServerClientTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerClient';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'CLIENT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsOfClientKill()
    {
        $arguments = array('kill', '127.0.0.1:45393');
        $expected = array('kill', '127.0.0.1:45393');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsOfClientList()
    {
        $arguments = array('list');
        $expected = array('list');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsOfClientGetname()
    {
        $arguments = $expected = array('getname');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsOfClientSetname()
    {
        $arguments = $expected = array('setname', 'connection-a');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponseOfClientKill()
    {
        $command = $this->getCommand();
        $command->setArguments(array('kill'));

        $this->assertSame(true, $command->parseResponse(true));
    }

    /**
     * @group disconnected
     */
    public function testParseResponseOfClientList()
    {
        $command = $this->getCommand();
        $command->setArguments(array('list'));

        $raw =<<<BUFFER
addr=127.0.0.1:45393 fd=6 idle=0 flags=N db=0 sub=0 psub=0
addr=127.0.0.1:45394 fd=7 idle=0 flags=N db=0 sub=0 psub=0
addr=127.0.0.1:45395 fd=8 idle=0 flags=N db=0 sub=0 psub=0

BUFFER;

        $parsed = array (
            array('addr'=>'127.0.0.1:45393','fd'=>'6','idle'=>'0','flags'=>'N','db'=>'0','sub'=>'0','psub'=>'0'),
            array('addr'=>'127.0.0.1:45394','fd'=>'7','idle'=>'0','flags'=>'N','db'=>'0','sub'=>'0','psub'=>'0'),
            array('addr'=>'127.0.0.1:45395','fd'=>'8','idle'=>'0','flags'=>'N','db'=>'0','sub'=>'0','psub'=>'0'),
        );

        $this->assertSame($parsed, $command->parseResponse($raw));
    }

    /**
     * @group connected
     */
    public function testReturnsListOfConnectedClients()
    {
        $redis = $this->getClient();

        $this->assertInternalType('array', $clients = $redis->client('LIST'));
        $this->assertGreaterThanOrEqual(1, count($clients));
        $this->assertInternalType('array', $clients[0]);
        $this->assertArrayHasKey('addr', $clients[0]);
        $this->assertArrayHasKey('fd', $clients[0]);
        $this->assertArrayHasKey('idle', $clients[0]);
        $this->assertArrayHasKey('flags', $clients[0]);
        $this->assertArrayHasKey('db', $clients[0]);
        $this->assertArrayHasKey('sub', $clients[0]);
        $this->assertArrayHasKey('psub', $clients[0]);
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.6.9
     */
    public function testGetsNameOfConnection()
    {
         $redis = $this->getClient();
         $clientName = $redis->client('GETNAME');
         $this->assertNull($clientName);

         $expectedConnectionName = 'foo-bar';
         $this->assertTrue($redis->client('SETNAME', $expectedConnectionName));
         $this->assertEquals($expectedConnectionName, $redis->client('GETNAME'));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.6.9
     */
    public function testSetsNameOfConnection()
    {
         $redis = $this->getClient();

         $expectedConnectionName = 'foo-baz';
         $this->assertTrue($redis->client('SETNAME', $expectedConnectionName));
         $this->assertEquals($expectedConnectionName, $redis->client('GETNAME'));
    }

    /**
     * @return array
     */
    public function invalidConnectionNameProvider()
    {
        return array(
            array('foo space'),
            array('foo \n'),
            array('foo $'),
        );
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.6.9
     * @dataProvider invalidConnectionNameProvider
     * @expectedException Predis\ServerException
     */
    public function testInvalidSetNameOfConnection($invalidConnectionName)
    {
         $redis = $this->getClient();
         $redis->client('SETNAME', $invalidConnectionName);
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     */
    public function testThrowsExceptioOnWrongModifier()
    {
        $redis = $this->getClient();

        $this->assertTrue($redis->client('FOO'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR No such client
     */
    public function testThrowsExceptionWhenKillingUnknownClient()
    {
        $redis = $this->getClient();

        $this->assertTrue($redis->client('KILL', '127.0.0.1:65535'));
    }
}
