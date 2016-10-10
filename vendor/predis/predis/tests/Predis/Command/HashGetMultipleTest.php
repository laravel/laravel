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
class HashGetMultipleTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\HashGetMultiple';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'HMGET';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 'field1', 'field2', 'field3');
        $expected = array('key', 'field1', 'field2', 'field3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsFieldsAsSingleArray()
    {
        $arguments = array('key', array('field1', 'field2', 'field3'));
        $expected = array('key', 'field1', 'field2', 'field3');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('bar', 'piyo', 'wut');
        $expected = array('bar', 'piyo', 'wut');

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('key', 'field1', 'field2', 'field3');
        $expected = array('prefix:key', 'field1', 'field2', 'field3');

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
    public function testReturnsValuesOfSpecifiedFields()
    {
        $redis = $this->getClient();

        $redis->hmset('metavars', 'foo', 'bar', 'hoge', 'piyo', 'lol', 'wut');

        $this->assertSame(array('bar', 'piyo', null), $redis->hmget('metavars', 'foo', 'hoge', 'unknown'));
        $this->assertSame(array('bar', 'bar'), $redis->hmget('metavars', 'foo', 'foo'));
        $this->assertSame(array(null, null), $redis->hmget('metavars', 'unknown', 'unknown'));
        $this->assertSame(array(null, null), $redis->hmget('unknown', 'foo', 'hoge'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');
        $redis->hmget('foo', 'bar');
    }
}
