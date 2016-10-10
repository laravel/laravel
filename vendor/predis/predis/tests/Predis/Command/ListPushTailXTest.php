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
 * @group realm-list
 */
class ListPushTailXTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ListPushTailX';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'RPUSHX';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 'value');
        $expected = array('key', 'value');

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
    public function testPushesElementsToHeadOfExistingList()
    {
        $redis = $this->getClient();

        $redis->rpush('metavars', 'foo');

        $this->assertSame(2, $redis->rpushx('metavars', 'hoge'));
        $this->assertSame(array('foo', 'hoge'), $redis->lrange('metavars', 0, -1));
    }

    /**
     * @group connected
     */
    public function testDoesNotPushElementOnNonExistingKey()
    {
        $redis = $this->getClient();

        $this->assertSame(0, $redis->rpushx('metavars', 'foo'));
        $this->assertSame(0, $redis->rpushx('metavars', 'hoge'));
        $this->assertFalse($redis->exists('metavars'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('metavars', 'foo');
        $redis->rpushx('metavars', 'hoge');
    }
}
