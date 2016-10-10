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
class KeyTypeTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\KeyType';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'TYPE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key');
        $expected = array('key');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame('type', $this->getCommand()->parseResponse('type'));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key');
        $expected = array('prefix:key');

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
    public function testReturnsTypeOfKey()
    {
        $redis = $this->getClient();

        $this->assertSame('none', $redis->type('type:keydoesnotexist'));

        $redis->set('type:string', 'foobar');
        $this->assertSame('string', $redis->type('type:string'));

        $redis->lpush('type:list', 'foobar');
        $this->assertSame('list', $redis->type('type:list'));

        $redis->sadd('type:set', 'foobar');
        $this->assertSame('set', $redis->type('type:set'));

        $redis->zadd('type:zset', 0, 'foobar');
        $this->assertSame('zset', $redis->type('type:zset'));

        $redis->hset('type:hash', 'foo', 'bar');
        $this->assertSame('hash', $redis->type('type:hash'));
    }
}
