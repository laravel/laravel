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
class ServerCommandTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerCommand';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'COMMAND';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('INFO', 'DEL');
        $expected = array('INFO', 'DEL');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array(
            array('get', 2, array('readonly', 'fast'), 1, 1, 1),
            array('set', -3, array('write', 'denyoom'), 1, 1, 1),
            array('watch', -2, array('readonly', 'noscript', 'fast'), 1, -1, 1),
            array('unwatch', 1, array('readonly', 'noscript', 'fast'), 0, 0, 0),
            array('info', -1, array('readonly', 'loading', 'stale'), 0, 0, 0),
        );

        $expected = $raw;

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testParseEmptyResponse()
    {
        $raw = array(null);
        $expected = array(null);

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.13
     */
    public function testReturnsEmptyCommandInfoOnNonExistingCommand()
    {
        $redis = $this->getClient();

        $this->assertCount(1, $response = $redis->command('INFO', 'FOOBAR'));
        $this->assertSame(array(null), $response);
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.13
     */
    public function testReturnsCommandInfoOnExistingCommand()
    {
        $redis = $this->getClient();

        $expected = array(array('command', 0, array('readonly', 'loading', 'stale'), 0, 0, 0));
        $this->assertCount(1, $response = $redis->command('INFO', 'COMMAND'));
        $this->assertSame($expected, $response);
    }

    /**
     * @group connected
     * @requiresRedisVersion >= 2.8.13
     */
    public function testReturnsListOfCommandInfoWithNoArguments()
    {
        $redis = $this->getClient();

        $this->assertGreaterThan(100, count($response = $redis->command()));
    }
}
