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
class StringSetMultiplePreserveTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringSetMultiplePreserve';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'MSETNX';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('foo', 'bar', 'hoge', 'piyo');
        $expected = array('foo', 'bar', 'hoge', 'piyo');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsAsSingleNamedArray()
    {
        $arguments = array(array('foo' => 'bar', 'hoge' => 'piyo'));
        $expected = array('foo', 'bar', 'hoge', 'piyo');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame(true, $this->getCommand()->parseResponse(true));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('foo', 'bar', 'hoge', 'piyo');
        $expected = array('prefix:foo', 'bar', 'prefix:hoge', 'piyo');

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
    public function testCreatesMultipleKeys()
    {
        $redis = $this->getClient();

        $this->assertTrue($redis->msetnx('foo', 'bar', 'hoge', 'piyo'));
        $this->assertSame('bar', $redis->get('foo'));
        $this->assertSame('piyo', $redis->get('hoge'));
    }

    /**
     * @group connected
     */
    public function testCreatesMultipleKeysAndPreservesExistingOnes()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');

        $this->assertFalse($redis->msetnx('foo', 'barbar', 'hoge', 'piyo'));
        $this->assertSame('bar', $redis->get('foo'));
        $this->assertFalse($redis->exists('hoge'));
    }
}
