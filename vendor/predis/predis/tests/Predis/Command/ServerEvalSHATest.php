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
class ServerEvalSHATest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerEvalSHA';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'EVALSHA';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('9d0c0826bde023cc39eebaaf832c32a890f3b088', 1, 'foo', 'bar');
        $expected = array('9d0c0826bde023cc39eebaaf832c32a890f3b088', 1, 'foo', 'bar');

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
        $sha1 = 'a42059b356c875f0717db19a51f6aaca9ae659ea';

        $arguments = array($sha1, 2, 'foo', 'hoge', 'bar', 'piyo');
        $expected = array($sha1, 2, 'prefix:foo', 'prefix:hoge', 'bar', 'piyo');

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
        $command = $this->getCommandWithArgumentsArray(array($sha1 = sha1('return true')), 0);
        $this->assertSame($sha1, $command->getScriptHash());
    }

    /**
     * @group connected
     */
    public function testExecutesSpecifiedLuaScript()
    {
        $redis = $this->getClient();

        $lua = 'return {KEYS[1],KEYS[2],ARGV[1],ARGV[2]}';
        $sha1 = sha1($lua);
        $result = array('foo', 'hoge', 'bar', 'piyo');

        $this->assertSame($result, $redis->eval($lua, 2, 'foo', 'hoge', 'bar', 'piyo'));
        $this->assertSame($result, $redis->evalsha($sha1, 2, 'foo', 'hoge', 'bar', 'piyo'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     */
    public function testThrowsExceptionOnWrongNumberOfKeys()
    {
        $redis = $this->getClient();

        $lua = 'return {KEYS[1],KEYS[2],ARGV[1],ARGV[2]}';
        $sha1 = sha1($lua);

        $redis->eval($lua, 2, 'foo', 'hoge', 'bar', 'piyo');
        $redis->evalsha($sha1, 3, 'foo', 'hoge');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     */
    public function testThrowsExceptionOnInvalidScript()
    {
        $redis = $this->getClient();

        $redis->evalsha('ffffffffffffffffffffffffffffffffffffffff', 0);
    }
}
