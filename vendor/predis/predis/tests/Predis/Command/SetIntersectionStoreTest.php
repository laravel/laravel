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
class SetIntersectionStoreTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\SetIntersectionStore';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SINTERSTORE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key:destination', 'key:source1', 'key:source:2');
        $expected = array('key:destination', 'key:source1', 'key:source:2');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsSourceKeysAsSingleArray()
    {
        $arguments = array('key:destination', array('key:source1', 'key:source:2'));
        $expected = array('key:destination', 'key:source1', 'key:source:2');

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
        $arguments = array('key:destination', 'key:source1', 'key:source:2');
        $expected = array('prefix:key:destination', 'prefix:key:source1', 'prefix:key:source:2');

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
    public function testStoresMembersOfSetOnSingleKey()
    {
        $redis = $this->getClient();

        $redis->sadd('letters:1st', 'a', 'b', 'c', 'd', 'e', 'f', 'g');

        $this->assertSame(7, $redis->sinterstore('letters:destination', 'letters:1st'));
        $this->assertSameValues(array( 'a', 'b', 'c', 'd', 'e', 'f', 'g'), $redis->smembers('letters:destination'));
    }

    /**
     * @group connected
     */
    public function testDoesNotStoreOnNonExistingSetForIntersection()
    {
        $redis = $this->getClient();

        $redis->sadd('letters:1st', 'a', 'b', 'c', 'd', 'e', 'f', 'g');

        $this->assertSame(0, $redis->sinterstore('letters:destination', 'letters:1st', 'letters:2nd'));
        $this->assertFalse($redis->exists('letters:destination'));
    }

    /**
     * @group connected
     */
    public function testStoresIntersectionOfMultipleSets()
    {
        $redis = $this->getClient();

        $redis->sadd('letters:1st', 'a', 'b', 'c', 'd', 'e', 'f', 'g');
        $redis->sadd('letters:2nd', 'a', 'c', 'f', 'g');
        $redis->sadd('letters:3rd', 'a', 'b', 'e', 'f');

        $this->assertSame(4, $redis->sinterstore('letters:destination', 'letters:1st', 'letters:2nd'));
        $this->assertSameValues(array('a', 'c', 'f', 'g'), $redis->smembers('letters:destination'));

        $this->assertSame(2, $redis->sinterstore('letters:destination', 'letters:1st', 'letters:2nd', 'letters:3rd'));
        $this->assertSameValues(array('a', 'f'), $redis->smembers('letters:destination'));
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
        $redis->sinterstore('set:destination', 'set:source');
    }
}
