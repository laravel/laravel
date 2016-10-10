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

/**
 * @group ext-phpiredis
 */
class PhpiredisStreamConnectionTest extends PredisConnectionTestCase
{
    /**
     * @group disconnected
     */
    public function testConstructorDoesNotOpenConnection()
    {
        $connection = new PhpiredisStreamConnection($this->getParameters());

        $this->assertFalse($connection->isConnected());
    }

    /**
     * @group disconnected
     */
    public function testExposesParameters()
    {
        $parameters = $this->getParameters();
        $connection = new PhpiredisStreamConnection($parameters);

        $this->assertSame($parameters, $connection->getParameters());
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid scheme: udp
     */
    public function testThrowsExceptionOnInvalidScheme()
    {
        $parameters = $this->getParameters(array('scheme' => 'udp'));
        $connection = new PhpiredisStreamConnection($parameters);
    }

    /**
     * @group disconnected
     */
    public function testCanBeSerialized()
    {
        $parameters = $this->getParameters(array('alias' => 'redis', 'read_write_timeout' => 10));
        $connection = new PhpiredisStreamConnection($parameters);

        $unserialized = unserialize(serialize($connection));

        $this->assertInstanceOf('Predis\Connection\PhpiredisStreamConnection', $unserialized);
        $this->assertEquals($parameters, $unserialized->getParameters());
    }

    // ******************************************************************** //
    // ---- INTEGRATION TESTS --------------------------------------------- //
    // ******************************************************************** //

    /**
     * @group connected
     */
    public function testAcceptsTcpNodelayParameter()
    {
        if (!version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $this->markTestSkipped('Setting TCP_NODELAY on PHP socket streams works on PHP >= 5.4.0');
        }

        $connection = new PhpiredisStreamConnection($this->getParameters(array('tcp_nodelay' => false)));
        $connection->connect();
        $this->assertTrue($connection->isConnected());

        $connection = new PhpiredisStreamConnection($this->getParameters(array('tcp_nodelay' => true)));
        $connection->connect();
        $this->assertTrue($connection->isConnected());
    }

    /**
     * @group connected
     */
    public function testExecutesCommandsOnServer()
    {
        $connection = $this->getConnection($profile, true);

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
     * @group connected
     * @expectedException Predis\Protocol\ProtocolException
     * @expectedExceptionMessage Protocol error, got "P" as reply type byte
     */
    public function testThrowsExceptionOnProtocolDesynchronizationErrors()
    {
        $connection = $this->getConnection($profile);
        $socket = $connection->getResource();

        $connection->writeCommand($profile->createCommand('ping'));
        fread($socket, 1);

        $connection->read();
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * {@inheritdoc}
     */
    protected function getConnection(&$profile = null, $initialize = false, Array $parameters = array())
    {
        $parameters = $this->getParameters($parameters);
        $profile = $this->getProfile();

        $connection = new PhpiredisStreamConnection($parameters);

        if ($initialize) {
            $connection->pushInitCommand($profile->createCommand('select', array($parameters->database)));
            $connection->pushInitCommand($profile->createCommand('flushdb'));
        }

        return $connection;
    }
}
