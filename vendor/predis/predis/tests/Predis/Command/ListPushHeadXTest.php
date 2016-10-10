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
class ListPushHeadXTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ListPushHeadX';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'LPUSHX';
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

        $redis->lpush('metavars', 'foo');

        $this->assertSame(2, $redis->lpushx('metavars', 'hoge'));
        $this->assertSame(array('hoge', 'foo'), $redis->lrange('metavars', 0, -1));
    }

    /**
     * @group connected
     */
    public function testDoesNotPushElementOnNonExistingKey()
    {
        $redis = $this->getClient();

        $this->assertSame(0, $redis->lpushx('metavars', 'foo'));
        $this->assertSame(0, $redis->lpushx('metavars', 'hoge'));
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
        $redis->lpushx('metavars', 'hoge');
    }
}
