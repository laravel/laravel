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
class SetUnionTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\SetUnion';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SUNION';
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
    public function testReturnsMembersOnSingleKeyOrNonExistingSetForUnion()
    {
        $redis = $this->getClient();

        $redis->sadd('letters:1st', 'a', 'b', 'c', 'd', 'e', 'f', 'g');

        $this->assertSameValues(array( 'a', 'b', 'c', 'd', 'e', 'f', 'g'), $redis->sunion('letters:1st'));
        $this->assertSameValues(array( 'a', 'b', 'c', 'd', 'e', 'f', 'g'), $redis->sunion('letters:1st', 'letters:2nd'));
    }

    /**
     * @group connected
     */
    public function testReturnsMembersFromDifferenceAmongSets()
    {
        $redis = $this->getClient();

        $redis->sadd('letters:1st', 'b', 'd', 'f');
        $redis->sadd('letters:2nd', 'a', 'c', 'g');
        $redis->sadd('letters:3rd', 'a', 'e', 'f');

        $this->assertSameValues(array('a', 'c', 'e', 'f', 'g'), $redis->sunion('letters:2nd', 'letters:3rd'));
        $this->assertSameValues(array( 'a', 'b', 'c', 'd', 'e', 'f', 'g'), $redis->sunion('letters:1st', 'letters:2nd', 'letters:3rd'));
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
        $redis->sunion('set:foo');
    }
}
