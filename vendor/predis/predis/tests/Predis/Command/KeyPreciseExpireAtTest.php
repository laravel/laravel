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
class KeyPreciseExpireAtTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\KeyPreciseExpireAt';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'PEXPIREAT';
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
     * @medium
     * @group connected
     * @group slow
     */
    public function testCanExpireKeys()
    {
        $ttl = 1.5;
        $redis = $this->getClient();

        $redis->set('foo', 'bar');

        $this->assertTrue($redis->pexpireat('foo', time() + $ttl * 1000));
        $this->assertLessThan($ttl * 1000, $redis->pttl('foo'));

        $this->sleep($ttl + 0.5);

        $this->assertFalse($redis->exists('foo'));
    }

    /**
     * @group connected
     */
    public function testDeletesKeysOnPastUnixTime()
    {
        $redis = $this->getClient();

        $now = time();
        $redis->set('foo', 'bar');

        $this->assertTrue($redis->expireat('foo', time() - 100000));
        $this->assertFalse($redis->exists('foo'));
    }
}
