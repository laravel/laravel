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
 * In order to support the output of SLOWLOG, the backend connection
 * must be able to parse nested multibulk replies deeper than 2 levels.
 *
 * @group commands
 * @group realm-server
 */
class ServerSlowlogTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerSlowlog';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SLOWLOG';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('GET', '2');
        $expected = array('GET', '2');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array(array(0, 1323163469, 12451, array('SORT', 'list:unordered')));
        $expected = array(
            array(
                'id' => 0,
                'timestamp' => 1323163469,
                'duration' => 12451,
                'command' => array('SORT', 'list:unordered'),
            ),
        );

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group connected
     */
    public function testReturnsAnArrayOfLoggedCommands()
    {
        $redis = $this->getClient();

        $config = $redis->config('get', 'slowlog-log-slower-than');
        $threshold = array_pop($config);

        $redis->config('set', 'slowlog-log-slower-than', 0);
        $redis->set('foo', 'bar');

        $this->assertInternalType('array', $slowlog = $redis->slowlog('GET'));
        $this->assertGreaterThan(0, count($slowlog));

        $this->assertInternalType('array', $slowlog[0]);
        $this->assertGreaterThan(0, $slowlog[0]['id']);
        $this->assertGreaterThan(0, $slowlog[0]['timestamp']);
        $this->assertGreaterThan(0, $slowlog[0]['duration']);
        $this->assertInternalType('array', $slowlog[0]['command']);

        $redis->config('set', 'slowlog-log-slower-than', $threshold);
    }

    /**
     * @group connected
     */
    public function testCanResetTheLog()
    {
        $redis = $this->getClient();

        $this->assertTrue($redis->slowlog('RESET'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     */
    public function testThrowsExceptionOnInvalidSubcommand()
    {
        $redis = $this->getClient();

        $redis->slowlog('INVALID');
    }
}
