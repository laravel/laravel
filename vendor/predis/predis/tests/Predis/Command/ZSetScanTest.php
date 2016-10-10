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
 * @group realm-zset
 */
class ZSetScanTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ZSetScan';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'ZSCAN';
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
        $raw = array('3', array('member:1', '1', 'member:2', '2', 'member:3', '3'));
        $expected = array(3, array(array('member:1' , 1.0), array('member:2', 2.0), array('member:3' , 3.0)));

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
        $expectedMembers = array('member:one', 'member:two', 'member:three', 'member:four');
        $expectedScores = array(1.0, 2.0, 3.0, 4.0);

        $redis = $this->getClient();
        $redis->zadd('key', array_combine($expectedMembers, $expectedScores));

        $response = $redis->zscan('key', 0);

        $this->assertSame(0, $response[0]);
        $this->assertSame($expectedMembers, array_map(function ($e) { return $e[0]; }, $response[1]));
        $this->assertSame($expectedScores, array_map(function ($e) { return $e[1]; }, $response[1]));
    }

    /**
     * @group connected
     */
    public function testScanWithMatchingMembers()
    {
        $redis = $this->getClient();
        $redis->zadd('key', array('member:one' => 1.0, 'member:two' => 2.0, 'member:three' => 3.0, 'member:four' => 4.0));

        $response = $redis->zscan('key', 0, 'MATCH', 'member:t*');

        $this->assertSame(array('member:two', 'member:three'), array_map(function ($e) { return $e[0]; }, $response[1]));
        $this->assertSame(array(2.0, 3.0), array_map(function ($e) { return $e[1]; }, $response[1]));
    }

    /**
     * @group connected
     */
    public function testScanWithNoMatchingMembers()
    {
        $redis = $this->getClient();
        $redis->zadd('key', $members = array('member:one' => 1.0, 'member:two' => 2.0, 'member:three' => 3.0, 'member:four' => 4.0));

        $response = $redis->zscan('key', 0, 'MATCH', 'nomember:*');

        $this->assertSame(0, $response[0]);
        $this->assertEmpty($response[1]);
    }
}
