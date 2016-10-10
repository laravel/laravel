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
class StringBitCountTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringBitCount';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'BITCOUNT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 0, 10);
        $expected = array('key', 0, 10);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = 10;
        $expected = 10;

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 0, 10);
        $expected = array('prefix:key', 0, 10);

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
    public function testReturnsNumberOfBitsSet()
    {
        $redis = $this->getClient();

        $redis->setbit('key', 1, 1);
        $redis->setbit('key', 10, 1);
        $redis->setbit('key', 16, 1);
        $redis->setbit('key', 22, 1);
        $redis->setbit('key', 32, 1);

        $this->assertSame(5, $redis->bitcount('key'), 'Count bits set (without range)');
        $this->assertSame(3, $redis->bitcount('key', 2, 4), 'Count bits set (with range)');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->lpush('key', 'list');
        $redis->bitcount('key');
    }
}
