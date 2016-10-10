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
 * @group realm-set
 */
class SetRemoveTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\SetRemove';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SREM';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 'member1', 'member2', 'member3');
        $expected = array('key', 'member1', 'member2', 'member3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsMembersAsSingleArray()
    {
        $arguments = array('key', array('member1', 'member2', 'member3'));
        $expected = array('key', 'member1', 'member2', 'member3');

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
        $arguments = array('key', 'member1', 'member2', 'member3');
        $expected = array('prefix:key', 'member1', 'member2', 'member3');

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
    public function testRemovesMembersFromSet()
    {
        $redis = $this->getClient();

        $redis->sadd('letters', 'a', 'b', 'c', 'd');

        $this->assertSame(1, $redis->srem('letters', 'b'));
        $this->assertSame(1, $redis->srem('letters', 'd', 'z'));
        $this->assertSameValues(array('a', 'c'), $redis->smembers('letters'));

        $this->assertSame(0, $redis->srem('digits', 1));
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
        $redis->srem('foo', 'bar');
    }
}
