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
class KeyPreciseExpireTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\KeyPreciseExpire';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'PEXPIRE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 100);
        $expected = array('key', 100);

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

        $this->assertTrue($command->parseResponse(1));
        $this->assertFalse($command->parseResponse(0));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 'value');
        $expected = array('prefix:key', 'value');

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
    public function testReturnsFalseOnNonExistingKeys()
    {
        $redis = $this->getClient();

        $this->assertFalse($redis->pexpire('foo', 20000));
    }

    /**
     * @medium
     * @group connected
     * @group slow
     */
    public function testCanExpireKeys()
    {
        $ttl = 1 * 1000;
        $redis = $this->getClient();

        $redis->set('foo', 'bar');

        $this->assertTrue($redis->pexpire('foo', $ttl));

        $this->sleep(1.2);
        $this->assertFalse($redis->exists('foo'));
    }

    /**
     * @group connected
     * @group slow
     */
    public function testConsistencyWithTTL()
    {
        $ttl = 1 * 1000;
        $redis = $this->getClient();

        $redis->set('foo', 'bar');

        $this->assertTrue($redis->pexpire('foo', $ttl));

        $this->sleep(0.5);
        $this->assertLessThanOrEqual($ttl, $redis->pttl('foo'));
        $this->assertGreaterThan($ttl - 800, $redis->pttl('foo'));
    }

    /**
     * @group connected
     */
    public function testDeletesKeysOnNegativeTTL()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');

        $this->assertTrue($redis->pexpire('foo', -10000));
        $this->assertFalse($redis->exists('foo'));
    }
}
