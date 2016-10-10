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
 * @group ext-curl
 * @group ext-phpiredis
 * @group realm-connection
 * @group realm-webdis
 */
class WebdisConnectionTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testIsConnectedAlwaysReturnsTrue()
    {
        $connection = new WebdisConnection($this->getParameters());

        $this->assertTrue($connection->isConnected());
    }

    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage The method Predis\Connection\WebdisConnection::writeCommand() is not supported
     */
    public function testWritingCommandsIsNotSupported()
    {
        $connection = new WebdisConnection($this->getParameters());
        $connection->writeCommand($this->getProfile()->createCommand('ping'));
    }

    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage The method Predis\Connection\WebdisConnection::readResponse() is not supported
     */
    public function testReadingResponsesIsNotSupported()
    {
        $connection = new WebdisConnection($this->getParameters());
        $connection->readResponse($this->getProfile()->createCommand('ping'));
    }

    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage The method Predis\Connection\WebdisConnection::read() is not supported
     */
    public function testReadingFromConnectionIsNotSupported()
    {
        $connection = new WebdisConnection($this->getParameters());
        $connection->read();
    }

    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage The method Predis\Connection\WebdisConnection::pushInitCommand() is not supported
     */
    public function testPushingInitCommandsIsNotSupported()
    {
        $connection = new WebdisConnection($this->getParameters());
        $connection->pushInitCommand($this->getProfile()->createCommand('ping'));
    }

    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage Disabled command: SELECT
     */
    public function testRejectCommandSelect()
    {
        $connection = new WebdisConnection($this->getParameters());
        $connection->executeCommand($this->getProfile()->createCommand('select', array(0)));
    }

    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage Disabled command: AUTH
     */
    public function testRejectCommandAuth()
    {
        $connection = new WebdisConnection($this->getParameters());
        $connection->executeCommand($this->getProfile()->createCommand('auth', array('foobar')));
    }

    /**
     * @group disconnected
     */
    public function testCanBeSerialized()
    {
        $parameters = $this->getParameters(array('alias' => 'webdis'));
        $connection = new WebdisConnection($parameters);

        $unserialized = unserialize(serialize($connection));

        $this->assertInstanceOf('Predis\Connection\WebdisConnection', $unserialized);
        $this->assertEquals($parameters, $unserialized->getParameters());
    }

    // ******************************************************************** //
    // ---- INTEGRATION TESTS --------------------------------------------- //
    // ******************************************************************** //

    /**
     * @group connected
     */
    public function testExecutesCommandsOnServer()
    {
        $connection = $this->getConnection($profile);

        $cmdPing   = $profile->createCommand('ping');
        $cmdEcho   = $profile->createCommand('echo', array('echoed'));
        $cmdGet    = $profile->createCommand('get', array('foobar'));
        $cmdRpush  = $profile->createCommand('rpush', array('metavars', 'foo', 'hoge', 'lol'));
        $cmdLrange = $profile->createCommand('lrange', array('metavars', 0, -1));

        $this->assertSame('PONG', $connection->executeCommand($cmdPing));
        $this->assertSame('echoed', $connection->executeCommand($cmdEcho));
        $this->assertNull($connection->executeCommand($cmdGet));
        $this->assertSame(3, $connection->executeCommand($cmdRpush));
        $this->assertSame(array('foo', 'hoge', 'lol'), $connection->executeCommand($cmdLrange));
    }

    /**
     * @medium
     * @group disconnected
     * @group slow
     * @expectedException Predis\Connection\ConnectionException
     */
    public function testThrowExceptionWhenUnableToConnect()
    {
        $parameters = $this->getParameters(array('host' => '169.254.10.10'));
        $connection = new WebdisConnection($parameters);
        $connection->executeCommand($this->getProfile()->createCommand('ping'));
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
            'scheme' => 'http',
            'host' => WEBDIS_SERVER_HOST,
            'port' => WEBDIS_SERVER_PORT,
        );
    }

    /**
     * Returns a new instance of a connection instance.
     *
     * @param  mixed            $profile    Redis profile.
     * @param  array            $parameters Additional connection parameters.
     * @return WebdisConnection
     */
    protected function getConnection(&$profile = null, Array $parameters = array())
    {
        $parameters = $this->getParameters($parameters);
        $profile = $this->getProfile();

        $connection = new WebdisConnection($parameters);
        $connection->executeCommand($profile->createCommand('flushdb'));

        return $connection;
    }
}
