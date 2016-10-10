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
class ServerConfigTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerConfig';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'CONFIG';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('GET', 'slowlog');
        $expected = array('GET', 'slowlog');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponseOfConfigGet()
    {
        $raw = array('slowlog-log-slower-than','10000','slowlog-max-len','64','loglevel','verbose');
        $expected = array(
            'slowlog-log-slower-than' => '10000',
            'slowlog-max-len' => '64',
            'loglevel' => 'verbose',
        );

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testParseResponseOfConfigSet()
    {
        $command = $this->getCommand();

        $this->assertTrue($command->parseResponse(true));
    }

    /**
     * @group disconnected
     */
    public function testParseResponseOfConfigResetstat()
    {
        $command = $this->getCommand();

        $this->assertTrue($command->parseResponse(true));
    }

    /**
     * @group connected
     */
    public function testReturnsListOfConfigurationValues()
    {
        $redis = $this->getClient();

        $this->assertInternalType('array', $configs = $redis->config('GET', '*'));
        $this->assertGreaterThan(1, count($configs));
        $this->assertArrayHasKey('loglevel', $configs);
        $this->assertArrayHasKey('appendonly', $configs);
        $this->assertArrayHasKey('dbfilename', $configs);
    }

    /**
     * @group connected
     */
    public function testReturnsListOfOneConfigurationEntry()
    {
        $redis = $this->getClient();

        $this->assertInternalType('array', $configs = $redis->config('GET', 'dbfilename'));
        $this->assertEquals(1, count($configs));
        $this->assertArrayHasKey('dbfilename', $configs);
    }

    /**
     * @group connected
     */
    public function testReturnsEmptyListOnUnknownConfigurationEntry()
    {
        $redis = $this->getClient();

        $this->assertSame(array(), $redis->config('GET', 'foobar'));
    }

    /**
     * @group connected
     */
    public function testReturnsTrueOnSuccessfulConfiguration()
    {
        $redis = $this->getClient();

        $previous = $redis->config('GET', 'loglevel');

        $this->assertTrue($redis->config('SET', 'loglevel', 'notice'));
        $this->assertSame(array('loglevel' => 'notice'), $redis->config('GET', 'loglevel'));

        // We set the loglevel configuration to the previous value.
        $redis->config('SET', 'loglevel', $previous['loglevel']);
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR Unsupported CONFIG parameter: foo
     */
    public function testThrowsExceptionWhenSettingUnknownConfiguration()
    {
        $redis = $this->getClient();

        $this->assertFalse($redis->config('SET', 'foo', 'bar'));
    }

    /**
     * @group connected
     */
    public function testReturnsTrueOnResetstat()
    {
        $redis = $this->getClient();

        $this->assertTrue($redis->config('RESETSTAT'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     */
    public function testThrowsExceptionOnUnknownSubcommand()
    {
        $redis = $this->getClient();

        $this->assertFalse($redis->config('FOO'));
    }
}
