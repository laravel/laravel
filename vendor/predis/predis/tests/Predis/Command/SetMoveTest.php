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
class SetMoveTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\SetMove';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SMOVE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key:source', 'key:destination', 'member');
        $expected = array('key:source', 'key:destination', 'member');

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
        $arguments = array('key:source', 'key:destination', 'member');
        $expected = array('prefix:key:source', 'prefix:key:destination', 'member');

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
    public function testReturnsMemberExistenceInSet()
    {
        $redis = $this->getClient();

        $redis->sadd('letters:source', 'a', 'b', 'c');

        $this->assertTrue($redis->smove('letters:source', 'letters:destination', 'b'));
        $this->assertFalse($redis->smove('letters:source', 'letters:destination', 'z'));

        $this->assertSameValues(array('a', 'c'), $redis->smembers('letters:source'));
        $this->assertSameValues(array('b'), $redis->smembers('letters:destination'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongTypeOfSourceKey()
    {
        $redis = $this->getClient();

        $redis->set('set:source', 'foo');
        $redis->sadd('set:destination', 'bar');
        $redis->smove('set:destination', 'set:source', 'foo');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongTypeOfDestinationKey()
    {
        $redis = $this->getClient();

        $redis->sadd('set:source', 'foo');
        $redis->set('set:destination', 'bar');
        $redis->smove('set:destination', 'set:source', 'foo');
    }
}
