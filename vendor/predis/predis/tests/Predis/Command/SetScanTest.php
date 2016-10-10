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
class SetScanTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\SetScan';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SSCAN';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 0, 'MATCH', 'member:*', 'COUNT', 10);
        $expected = array('key', 0, 'MATCH', 'member:*', 'COUNT', 10);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsBasicUsage()
    {
        $arguments = array('key', 0);
        $expected = array('key', 0);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsWithOptionsArray()
    {
        $arguments = array('key', 0, array('match' => 'member:*', 'count' => 10));
        $expected = array('key', 0, 'MATCH', 'member:*', 'COUNT', 10);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('3', array('member:1', 'member:2', 'member:3'));
        $expected = array(3, array('member:1', 'member:2', 'member:3'));

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', '0', 'MATCH', 'member:*', 'COUNT', 10);
        $expected = array('prefix:key', '0', 'MATCH', 'member:*', 'COUNT', 10);

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
    public function testScanWithoutMatch()
    {
        $redis = $this->getClient();
        $redis->sadd('key', $members = array('member:one', 'member:two', 'member:three', 'member:four'));

        $response = $redis->sscan('key', 0);

        $this->assertSame(0, $response[0]);
        $this->assertSameValues($members, $response[1]);
    }

    /**
     * @group connected
     */
    public function testScanWithMatchingMembers()
    {
        $redis = $this->getClient();
        $redis->sadd('key', $members = array('member:one', 'member:two', 'member:three', 'member:four'));

        $response = $redis->sscan('key', 0, 'MATCH', 'member:t*');

        $this->assertSameValues(array('member:two', 'member:three'), $response[1]);
    }

    /**
     * @group connected
     */
    public function testScanWithNoMatchingMembers()
    {
        $redis = $this->getClient();
        $redis->sadd('key', $members = array('member:one', 'member:two', 'member:three', 'member:four'));

        $response = $redis->sscan('key', 0, 'MATCH', 'nomember:*');

        $this->assertSame(0, $response[0]);
        $this->assertEmpty($response[1]);
    }
}
