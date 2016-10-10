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
class StringGetMultipleTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringGetMultiple';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'MGET';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key1', 'key2', 'key3');
        $expected = array('key1', 'key2', 'key3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsAsSingleArray()
    {
        $arguments = array(array('key1', 'key2', 'key3'));
        $expected = array('key1', 'key2', 'key3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('value1', 'value2', 'value3');
        $expected = array('value1', 'value2', 'value3');

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key1', 'key2', 'key3');
        $expected = array('prefix:key1', 'prefix:key2', 'prefix:key3');

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
    public function testReturnsArrayOfValues()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->set('hoge', 'piyo');

        $this->assertSame(array('bar', 'piyo'), $redis->mget('foo', 'hoge'));
    }

    /**
     * @group connected
     */
    public function testReturnsArrayWithNullValuesOnNonExistingKeys()
    {
        $redis = $this->getClient();

        $this->assertSame(array(null, null), $redis->mget('foo', 'hoge'));
    }

    /**
     * @group connected
     */
    public function testDoesNotThrowExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->lpush('metavars', 'foo');
        $this->assertSame(array(null), $redis->mget('metavars'));
    }
}
