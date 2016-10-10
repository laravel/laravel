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
 * @group realm-key
 */
class KeyScanTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\KeyScan';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SCAN';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array(0, 'MATCH', 'key:*', 'COUNT', 5);
        $expected = array(0, 'MATCH', 'key:*', 'COUNT', 5);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsBasicUsage()
    {
        $arguments = array(0);
        $expected = array(0);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsWithOptionsArray()
    {
        $arguments = array(0, array('match' => 'key:*', 'count' => 5));
        $expected = array(0, 'MATCH', 'key:*', 'COUNT', 5);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('3', array('key:1', 'key:2', 'key:3'));
        $expected = array(3, array('key:1', 'key:2', 'key:3'));

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group connected
     */
    public function testScanWithoutMatch()
    {
        $kvs = array('key:one' => 'one', 'key:two' => 'two', 'key:three' => 'three', 'key:four' => 'four');

        $redis = $this->getClient();
        $redis->mset($kvs);

        $response = $redis->scan(0);

        $this->assertSameValues(array_keys($kvs), $response[1]);
    }

    /**
     * @group connected
     */
    public function testScanWithMatchingKeys()
    {
        $kvs = array('key:one' => 'one', 'key:two' => 'two', 'key:three' => 'three', 'key:four' => 'four');

        $redis = $this->getClient();
        $redis->mset($kvs);

        $response = $redis->scan(0, 'MATCH', 'key:t*');

        $this->assertSameValues(array('key:two', 'key:three'), $response[1]);
    }

    /**
     * @group connected
     */
    public function testScanWithNoMatchingKeys()
    {
        $kvs = array('key:one' => 'one', 'key:two' => 'two', 'key:three' => 'three', 'key:four' => 'four');

        $redis = $this->getClient();
        $redis->mset($kvs);

        $response = $redis->scan(0, 'MATCH', 'nokey:*');

        $this->assertSame(0, $response[0]);
        $this->assertEmpty($response[1]);
    }
}
