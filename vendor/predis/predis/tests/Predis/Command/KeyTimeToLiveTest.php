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
 * @group realm-key
 */
class KeyTimeToLiveTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\KeyTimeToLive';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'TTL';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 10);
        $expected = array('key', 10);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $command = $this->getCommand();

        $this->assertSame(100, $command->parseResponse(100));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 10);
        $expected = array('prefix:key', 10);

        $command = $this->getCommandWithArgumentsArray($arguments);
        $command->prefixKeys('prefix:');

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeysIgnoredOnEmptyArguments()
    {
        $command = $this->getCommand();
        $command->prefixKeys('prefix:');

        $this->assertSame(array(), $command->getArguments());
    }

    /**
     * @group connected
     */
    public function testReturnsTTL()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->expire('foo', 10);

        $this->assertSame(10, $redis->ttl('foo'));
    }

    /**
     * @group connected
     */
    public function testReturnsLessThanZeroOnNonExpiringKeys()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $this->assertSame(-1, $redis->ttl('foo'));
    }

    /**
     * @group connected
     */
    public function testReturnsLessThanZeroOnNonExistingKeys()
    {
        $this->executeOnRedisVersion('2.8.0', '<', function ($test) {
            $test->assertSame(-1, $test->getClient()->ttl('foo'));
        });

        $this->executeOnRedisVersion('2.8.0', '>=', function ($test) {
            $test->assertSame(-2, $test->getClient()->ttl('foo'));
        });
    }
}
