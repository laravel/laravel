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
class StringStrlenTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringStrlen';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'STRLEN';
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
        $this->assertSame(4, $this->getCommand()->parseResponse(4));
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
    public function testReturnsTheLengthOfString()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $this->assertSame(3, $redis->strlen('foo'));

        $redis->append('foo', 'bar');
        $this->assertSame(6, $redis->strlen('foo'));
    }

    /**
     * @group connected
     */
    public function testReturnsZeroOnNonExistingKeys()
    {
        $redis = $this->getClient();

        $this->assertSame(0, $redis->strlen('foo'));
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
        $redis->strlen('metavars');
    }
}
