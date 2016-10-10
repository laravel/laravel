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
 * @group realm-zset
 */
class ZSetAddTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ZSetAdd';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'ZADD';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 1, 'member1', 2, 'member2');
        $expected = array('key', 1, 'member1', 2, 'member2');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsMembersScoresAsSingleArray()
    {
        $arguments = array('key', array('member1' => 1, 'member2' => 2));
        $expected = array('key', 1, 'member1', 2, 'member2');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame(1, $this->getCommand()->parseResponse(1));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 'score1', 'member1', 'score2', 'member2');
        $expected = array('prefix:key', 'score1', 'member1', 'score2', 'member2');

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
    public function testAddsOrUpdatesMembersOrderingByScore()
    {
        $redis = $this->getClient();

        $this->assertSame(5, $redis->zadd('letters', 1, 'a', 2, 'b', 3, 'c', 4, 'd', 5, 'e'));
        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $redis->zrange('letters', 0, -1));

        $this->assertSame(1, $redis->zadd('letters', 1, 'e', 8, 'c', 6, 'f'));
        $this->assertSame(array('a', 'e', 'b', 'd', 'f', 'c'), $redis->zrange('letters', 0, -1));
    }

    /**
     * @group connected
     */
    public function testAcceptsFloatValuesAsScore()
    {
        $redis = $this->getClient();

        $redis->zadd('letters', 0.2, 'b', 0.3, 'a', 0.1, 'c');
        $this->assertSame(array('c', 'b', 'a'), $redis->zrange('letters', 0, -1));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->zadd('foo', 10, 'bar');
    }
}
