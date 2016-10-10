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
 * @group realm-string
 */
class StringGetRangeTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringGetRange';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'GETRANGE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 5, 10);
        $expected = array('key', 5, 10);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame('substring',$this->getCommand()->parseResponse('substring'));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 5, 10);
        $expected = array('prefix:key', 5, 10);

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
    public function testReturnsSubstring()
    {
        $redis = $this->getClient();

        $redis->set('string', 'this is a string');

        $this->assertSame('this', $redis->getrange('string', 0, 3));
        $this->assertSame('ing', $redis->getrange('string', -3, -1));
        $this->assertSame('this is a string', $redis->getrange('string', 0, -1));
        $this->assertSame('string', $redis->getrange('string', 10, 100));

        $this->assertSame('t', $redis->getrange('string', 0, 0));
        $this->assertSame('', $redis->getrange('string', -1, 0));
    }

    /**
     * @group connected
     */
    public function testReturnsEmptyStringOnNonExistingKey()
    {
        $redis = $this->getClient();

        $this->assertSame('', $redis->getrange('string', 0, 3));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->lpush('metavars', 'foo');
        $redis->getrange('metavars', 0, 5);
    }
}
