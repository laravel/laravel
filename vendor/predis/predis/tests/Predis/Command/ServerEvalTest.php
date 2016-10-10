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
 * @group realm-scripting
 */
class ServerEvalTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerEval';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'EVAL';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('return redis.call("SET", KEYS[1], ARGV[1])', 1, 'foo', 'bar');
        $expected = array('return redis.call("SET", KEYS[1], ARGV[1])', 1, 'foo', 'bar');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $command = $this->getCommand();

        $this->assertSame('bar', $this->getCommand()->parseResponse('bar'));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $lua = 'return {KEYS[1],KEYS[2],ARGV[1],ARGV[2]}';

        $arguments = array($lua, 2, 'foo', 'hoge', 'bar', 'piyo');
        $expected = array($lua, 2, 'prefix:foo', 'prefix:hoge', 'bar', 'piyo');

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
     * @group disconnected
     */
    public function testGetScriptHash()
    {
        $command = $this->getCommandWithArgumentsArray(array($lua = 'return true', 0));
        $this->assertSame(sha1($lua), $command->getScriptHash());
    }

    /**
     * @group connected
     */
    public function testExecutesSpecifiedLuaScript()
    {
        $redis = $this->getClient();

        $lua = 'return {KEYS[1],KEYS[2],ARGV[1],ARGV[2]}';
        $result = array('foo', 'hoge', 'bar', 'piyo');

        $this->assertSame($result, $redis->eval($lua, 2, 'foo', 'hoge', 'bar', 'piyo'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     */
    public function testThrowsExceptionOnWrongNumberOfKeys()
    {
        $redis = $this->getClient();
        $lua = 'return {KEYS[1],KEYS[2],ARGV[1],ARGV[2]}';

        $redis->eval($lua, 3, 'foo', 'hoge');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     */
    public function testThrowsExceptionOnInvalidScript()
    {
        $redis = $this->getClient();

        $redis->eval('invalid', 0);
    }
}
