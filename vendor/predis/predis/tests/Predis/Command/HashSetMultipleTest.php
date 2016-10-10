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
class HashSetMultipleTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\HashSetMultiple';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'HMSET';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 'field1', 'value1', 'field2', 'value2');
        $expected = array('key', 'field1', 'value1', 'field2', 'value2');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsFieldsValuesAsSingleArray()
    {
        $arguments = array('key', array('field1' => 'value1', 'field2' => 'value2'));
        $expected = array('key', 'field1', 'value1', 'field2', 'value2');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertTrue($this->getCommand()->parseResponse(true));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 'field1', 'value1', 'field2', 'value2');
        $expected = array('prefix:key', 'field1', 'value1', 'field2', 'value2');

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
    public function testSetsSpecifiedFieldsOfHash()
    {
        $redis = $this->getClient();

        $this->assertTrue($redis->hmset('metavars', 'foo', 'bar', 'hoge', 'piyo'));
        $this->assertSame(array('foo' => 'bar', 'hoge' => 'piyo'), $redis->hgetall('metavars'));

        $this->assertTrue($redis->hmset('metavars', 'foo', 'barbar', 'lol', 'wut'));
        $this->assertSame(array('foo' => 'barbar', 'hoge' => 'piyo', 'lol' => 'wut'), $redis->hgetall('metavars'));
    }

    /**
     * @group connected
     */
    public function testSetsTheSpecifiedFie()
    {
        $redis = $this->getClient();

        $redis->hmset('metavars', 'foo', 'bar', 'hoge', 'piyo', 'lol', 'wut');

        $this->assertSame(array('foo' => 'bar', 'hoge' => 'piyo', 'lol' => 'wut'), $redis->hgetall('metavars'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('metavars', 'bar');
        $redis->hmset('metavars', 'foo', 'bar');
    }
}
