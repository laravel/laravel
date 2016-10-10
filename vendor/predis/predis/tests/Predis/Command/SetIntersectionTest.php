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
class SetIntersectionTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\SetIntersection';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SINTER';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key1', 'key2', 'key3');
        $expected = array('key1', 'key2', 'key3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsAsSingleArray()
    {
        $arguments = array(array('key1', 'key2', 'key3'));
        $expected = array('key1', 'key2', 'key3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('member1', 'member2', 'member3');
        $expected = array('member1', 'member2', 'member3');

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key1', 'key2', 'key3');
        $expected = array('prefix:key1', 'prefix:key2', 'prefix:key3');

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
    public function testReturnsMembersOfSetOnSingleKey()
    {
        $redis = $this->getClient();

        $redis->sadd('letters:1st', 'a', 'b', 'c', 'd', 'e', 'f', 'g');

        $this->assertSameValues(array('a', 'b', 'c', 'd', 'e', 'f', 'g'), $redis->sinter('letters:1st'));
    }

    /**
     * @group connected
     */
    public function testReturnsEmptyArrayOnNonExistingSetForIntersection()
    {
        $redis = $this->getClient();

        $redis->sadd('letters:1st', 'a', 'b', 'c', 'd', 'e', 'f', 'g');

        $this->assertSameValues(array(), $redis->sinter('letters:1st', 'letters:2nd'));
    }

    /**
     * @group connected
     */
    public function testReturnsMembersFromIntersectionAmongSets()
    {
        $redis = $this->getClient();

        $redis->sadd('letters:1st', 'a', 'b', 'c', 'd', 'e', 'f', 'g');
        $redis->sadd('letters:2nd', 'a', 'c', 'f', 'g');
        $redis->sadd('letters:3rd', 'a', 'b', 'e', 'f');

        $this->assertSameValues(array('a', 'c', 'f', 'g'), $redis->sinter('letters:1st', 'letters:2nd'));
        $this->assertSameValues(array('a', 'f'), $redis->sinter('letters:1st', 'letters:2nd', 'letters:3rd'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('set:foo', 'a');
        $redis->sinter('set:foo');
    }
}
