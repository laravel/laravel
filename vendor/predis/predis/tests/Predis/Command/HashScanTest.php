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
 * @group realm-hash
 */
class HashScanTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\HashScan';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'HSCAN';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 0, 'MATCH', 'field:*', 'COUNT', 10);
        $expected = array('key', 0, 'MATCH', 'field:*', 'COUNT', 10);

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
        $arguments = array('key', 0, array('match' => 'field:*', 'count' => 10));
        $expected = array('key', 0, 'MATCH', 'field:*', 'COUNT', 10);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('3', array('field:1', '1', 'field:2', '2', 'field:3', '3'));
        $expected = array(3, array('field:1' => '1', 'field:2' => '2', 'field:3' => '3'));

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', '0', 'MATCH', 'field:*', 'COUNT', 10);
        $expected = array('prefix:key', '0', 'MATCH', 'field:*', 'COUNT', 10);

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
        $expectedFields = array('field:one', 'field:two', 'field:three', 'field:four');
        $expectedValues = array('one', 'two', 'three', 'four');

        $redis = $this->getClient();
        $redis->hmset('key', array_combine($expectedFields, $expectedValues));

        $response = $redis->hscan('key', 0);

        $this->assertSame(0, $response[0]);
        $this->assertSame($expectedFields, array_keys($response[1]));
        $this->assertSame($expectedValues, array_values($response[1]));
    }

    /**
     * @group connected
     */
    public function testScanWithMatchingMembers()
    {
        $redis = $this->getClient();
        $redis->hmset('key', array('field:one' => 'one', 'field:two' => 'two', 'field:three' => 'three', 'field:four' => 'four'));

        $response = $redis->hscan('key', 0, 'MATCH', 'field:t*');

        $this->assertSame(array('field:two', 'field:three'), array_keys($response[1]));
        $this->assertSame(array('two', 'three'), array_values($response[1]));
    }

    /**
     * @group connected
     */
    public function testScanWithNoMatchingMembers()
    {
        $redis = $this->getClient();
        $redis->hmset('key', array('field:one' => 'one', 'field:two' => 'two', 'field:three' => 'three', 'field:four' => 'four'));

        $response = $redis->hscan('key', 0, 'MATCH', 'nofield:*');

        $this->assertSame(0, $response[0]);
        $this->assertEmpty($response[1]);
    }
}
