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
class StringBitOpTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringBitOp';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'BITOP';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('AND', 'key:dst', 'key:01', 'key:02');
        $expected = array('AND', 'key:dst', 'key:01', 'key:02');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsKeysAsSingleArray()
    {
        $arguments = array('AND', 'key:dst', array('key:01', 'key:02'));
        $expected = array('AND', 'key:dst', 'key:01', 'key:02');

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
        $arguments = array('AND', 'key:dst', 'key:01', 'key:02');
        $expected = array('AND', 'prefix:key:dst', 'prefix:key:01', 'prefix:key:02');

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
    public function testCanPerformBitwiseAND()
    {
        $redis = $this->getClient();

        $redis->set('key:src:1', "h\x80");
        $redis->set('key:src:2', "R");

        $this->assertSame(2, $redis->bitop('AND', 'key:dst', 'key:src:1', 'key:src:2'));
        $this->assertSame("@\x00", $redis->get('key:dst'));
    }

    /**
     * @group connected
     */
    public function testCanPerformBitwiseOR()
    {
        $redis = $this->getClient();

        $redis->set('key:src:1', "h\x80");
        $redis->set('key:src:2', "R");

        $this->assertSame(2, $redis->bitop('OR', 'key:dst', 'key:src:1', 'key:src:2'));
        $this->assertSame("z\x80", $redis->get('key:dst'));
    }

    /**
     * @group connected
     */
    public function testCanPerformBitwiseXOR()
    {
        $redis = $this->getClient();

        $redis->set('key:src:1', "h\x80");
        $redis->set('key:src:2', "R");

        $this->assertSame(2, $redis->bitop('XOR', 'key:dst', 'key:src:1', 'key:src:2'));
        $this->assertSame(":\x80", $redis->get('key:dst'));
    }

    /**
     * @group connected
     */
    public function testCanPerformBitwiseNOT()
    {
        $redis = $this->getClient();

        $redis->set('key:src:1', "h\x80");

        $this->assertSame(2, $redis->bitop('NOT', 'key:dst', 'key:src:1'));
        $this->assertSame("\x97\x7f", $redis->get('key:dst'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR BITOP NOT must be called with a single source key.
     */
    public function testBitwiseNOTAcceptsOnlyOneSourceKey()
    {
        $this->getClient()->bitop('NOT', 'key:dst', 'key:src:1', 'key:src:2');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR syntax error
     */
    public function testThrowsExceptionOnInvalidOperation()
    {
        $this->getClient()->bitop('NOOP', 'key:dst', 'key:src:1', 'key:src:2');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnInvalidSourceKey()
    {
        $redis = $this->getClient();

        $redis->lpush('key:src:1', 'list');
        $redis->bitop('AND', 'key:dst', 'key:src:1', 'key:src:2');
    }

    /**
     * @group connected
     */
    public function testDoesNotThrowExceptionOnInvalidDestinationKey()
    {
        $redis = $this->getClient();

        $redis->lpush('key:dst', 'list');
        $redis->bitop('AND', 'key:dst', 'key:src:1', 'key:src:2');

        $this->assertSame('none', $redis->type('key:dst'));
    }
}
