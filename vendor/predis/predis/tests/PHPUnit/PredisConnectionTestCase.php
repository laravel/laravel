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
use Predis\Profile\ServerProfile;

/**
 * @group realm-connection
 */
abstract class PredisConnectionTestCase extends PredisTestCase
{
    /**
     * @group disconnected
     * @group slow
     * @expectedException Predis\Connection\ConnectionException
     */
    public function testThrowExceptionWhenUnableToConnect()
    {
        $parameters = array('host' => '169.254.10.10', 'timeout' => 0.5);
        $connection = $this->getConnection($profile, false, $parameters);
        $connection->executeCommand($this->getProfile()->createCommand('ping'));
    }

    // ******************************************************************** //
    // ---- INTEGRATION TESTS --------------------------------------------- //
    // ******************************************************************** //

    /**
     * @group connected
     */
    public function testConnectForcesConnection()
    {
        $connection = $this->getConnection();

        $this->assertFalse($connection->isConnected());
        $connection->connect();
        $this->assertTrue($connection->isConnected());
    }

    /**
     * @group connected
     * @expectedException Predis\ClientException
     * @expectedExceptionMessage Connection already estabilished
     */
    public function testThrowsExceptionOnConnectWhenAlreadyConnected()
    {
        $connection = $this->getConnection();

        $connection->connect();
        $connection->connect();
    }

    /**
     * @group connected
     */
    public function testDisconnectForcesDisconnection()
    {
        $connection = $this->getConnection();

        $connection->connect();
        $this->assertTrue($connection->isConnected());

        $connection->disconnect();
        $this->assertFalse($connection->isConnected());
    }

    /**
     * @group disconnected
     */
    public function testDoesNotThrowExceptionOnDisconnectWhenAlreadyDisconnected()
    {
        $connection = $this->getConnection();

        $this->assertFalse($connection->isConnected());
        $connection->disconnect();
        $this->assertFalse($connection->isConnected());
    }

    /**
     * @group connected
     */
    public function testGetResourceForcesConnection()
    {
        $connection = $this->getConnection();

        $this->assertFalse($connection->isConnected());
        $this->assertInternalType('resource', $connection->getResource());
        $this->assertTrue($connection->isConnected());
    }

    /**
     * @group connected
     */
    public function testSendingCommandForcesConnection()
    {
        $connection = $this->getConnection($profile);
        $cmdPing = $profile->createCommand('ping');

        $this->assertSame('PONG', $connection->executeCommand($cmdPing));
        $this->assertTrue($connection->isConnected());
    }

    /**
     * @group connected
     */
    public function testExecutesCommandOnServer()
    {
        $connection = $this->getConnection($profile);

        $cmdPing = $this->getMock($profile->getCommandClass('ping'), array('parseResponse'));
        $cmdPing->expects($this->never())
                ->method('parseResponse');

        $this->assertSame('PONG', $connection->executeCommand($cmdPing));
    }

    /**
     * @group connected
     */
    public function testWritesCommandToServer()
    {
        $connection = $this->getConnection($profile);

        $cmdPing = $this->getMock($profile->getCommandClass('ping'), array('parseResponse'));
        $cmdPing->expects($this->never())
                ->method('parseResponse');

        $connection->writeCommand($cmdPing);
        $connection->disconnect();
    }

    /**
     * @group connected
     */
    public function testReadsCommandFromServer()
    {
        $connection = $this->getConnection($profile);

        $cmdPing = $this->getMock($profile->getCommandClass('ping'), array('parseResponse'));
        $cmdPing->expects($this->never())
                ->method('parseResponse');

        $connection->writeCommand($cmdPing);
        $this->assertSame('PONG', $connection->readResponse($cmdPing));
    }

    /**
     * @group connected
     */
    public function testIsAbleToWriteMultipleCommandsAndReadThemBackForPipelining()
    {
        $connection = $this->getConnection($profile);

        $cmdPing = $this->getMock($profile->getCommandClass('ping'), array('parseResponse'));
        $cmdPing->expects($this->never())
                ->method('parseResponse');

        $cmdEcho = $this->getMock($profile->getCommandClass('echo'), array('parseResponse'));
        $cmdEcho->setArguments(array('ECHOED'));
        $cmdEcho->expects($this->never())
                ->method('parseResponse');

        $connection = $this->getConnection();

        $connection->writeCommand($cmdPing);
        $connection->writeCommand($cmdEcho);

        $this->assertSame('PONG', $connection->readResponse($cmdPing));
        $this->assertSame('ECHOED', $connection->readResponse($cmdEcho));
    }

    /**
     * @group connected
     */
    public function testSendsInitializationCommandsOnConnection()
    {
        $connection = $this->getConnection($profile, true);

        $cmdPing = $this->getMock($profile->getCommandClass('ping'), array('getArguments'));
        $cmdPing->expects($this->once())
                ->method('getArguments')
                ->will($this->returnValue(array()));

        $cmdEcho = $this->getMock($profile->getCommandClass('echo'), array('getArguments'));
        $cmdEcho->expects($this->once())
                ->method('getArguments')
                ->will($this->returnValue(array('ECHOED')));

        $connection->pushInitCommand($cmdPing);
        $connection->pushInitCommand($cmdEcho);

        $connection->connect();
    }

    /**
     * @group connected
     */
    public function testReadsStatusReplies()
    {
        $connection = $this->getConnection($profile, true);

        $connection->writeCommand($profile->createCommand('set', array('foo', 'bar')));
        $this->assertTrue($connection->read());

        $connection->writeCommand($profile->createCommand('ping'));
        $this->assertSame('PONG', $connection->read());

        $connection->writeCommand($profile->createCommand('multi'));
        $connection->writeCommand($profile->createCommand('ping'));
        $this->assertTrue($connection->read());
        $this->assertInstanceOf('Predis\ResponseQueued', $connection->read());
    }

    /**
     * @group connected
     */
    public function testReadsBulkReplies()
    {
        $connection = $this->getConnection($profile, true);

        $connection->executeCommand($profile->createCommand('set', array('foo', 'bar')));

        $connection->writeCommand($profile->createCommand('get', array('foo')));
        $this->assertSame('bar', $connection->read());

        $connection->writeCommand($profile->createCommand('get', array('hoge')));
        $this->assertNull($connection->read());
    }

    /**
     * @group connected
     */
    public function testReadsIntegerReplies()
    {
        $connection = $this->getConnection($profile, true);

        $connection->executeCommand($profile->createCommand('rpush', array('metavars', 'foo', 'hoge', 'lol')));
        $connection->writeCommand($profile->createCommand('llen', array('metavars')));

        $this->assertSame(3, $connection->read());
    }

    /**
     * @group connected
     */
    public function testReadsErrorRepliesAsResponseErrorObjects()
    {
        $connection = $this->getConnection($profile, true);

        $connection->executeCommand($profile->createCommand('set', array('foo', 'bar')));
        $connection->writeCommand($profile->createCommand('rpush', array('foo', 'baz')));

        $this->assertInstanceOf('Predis\ResponseError', $error = $connection->read());
        $this->assertRegExp('/[ERR|WRONGTYPE] Operation against a key holding the wrong kind of value/', $error->getMessage());
    }

    /**
     * @group connected
     */
    public function testReadsMultibulkRepliesAsArrays()
    {
        $connection = $this->getConnection($profile, true);

        $connection->executeCommand($profile->createCommand('rpush', array('metavars', 'foo', 'hoge', 'lol')));
        $connection->writeCommand($profile->createCommand('lrange', array('metavars', 0, -1)));

        $this->assertSame(array('foo', 'hoge', 'lol'), $connection->read());
    }

    /**
     * @group connected
     * @group slow
     * @expectedException Predis\Connection\ConnectionException
     */
    public function testThrowsExceptionOnConnectionTimeout()
    {
        $connection = $this->getConnection($_, false, array('host' => '169.254.10.10', 'timeout' => 0.5));

        $connection->connect();
    }

    /**
     * @group connected
     * @group slow
     * @expectedException Predis\Connection\ConnectionException
     */
    public function testThrowsExceptionOnReadWriteTimeout()
    {
        $connection = $this->getConnection($profile, true, array('read_write_timeout' => 0.5));

        $connection->executeCommand($profile->createCommand('brpop', array('foo', 3)));
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Returns a named array with the default connection parameters and their values.
     *
     * @return Array Default connection parameters.
     */
    protected function getDefaultParametersArray()
    {
        return array(
            'scheme' => 'tcp',
            'host' => REDIS_SERVER_HOST,
            'port' => REDIS_SERVER_PORT,
            'database' => REDIS_SERVER_DBNUM,
            'read_write_timeout' => 2,
        );
    }

    /**
     * Returns a new instance of a connection instance.
     *
     * @param  ServerProfile    $profile    Reference to the server profile instance.
     * @param  bool             $initialize Push default initialization commands (SELECT and FLUSHDB).
     * @param  array            $parameters Additional connection parameters.
     * @return StreamConnection
     */
    abstract protected function getConnection(&$profile = null, $initialize = false, Array $parameters = array());
}
