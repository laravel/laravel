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
 * @group realm-pubsub
 */
class PubSubSubscribeByPatternTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\PubSubSubscribeByPattern';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'PSUBSCRIBE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('channel:foo:*', 'channel:hoge:*');
        $expected = array('channel:foo:*', 'channel:hoge:*');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsAsSingleArray()
    {
        $arguments = array(array('channel:foo:*', 'channel:hoge:*'));
        $expected = array('channel:foo:*', 'channel:hoge:*');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('psubscribe', 'channel:*', 1);
        $expected = array('psubscribe', 'channel:*', 1);

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array('channel:foo:*', 'channel:hoge:*');
        $expected = array('prefix:channel:foo:*', 'prefix:channel:hoge:*');

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
    public function testReturnsTheFirstPsubscribedChannelDetails()
    {
        $redis = $this->getClient();

        $this->assertSame(array('psubscribe', 'channel:*', 1), $redis->psubscribe('channel:*'));
    }

    /**
     * @group connected
     */
    public function testCanSendPsubscribeAfterPsubscribe()
    {
        $redis = $this->getClient();

        $this->assertSame(array('psubscribe', 'channel:foo:*', 1), $redis->psubscribe('channel:foo:*'));
        $this->assertSame(array('psubscribe', 'channel:hoge:*', 2), $redis->psubscribe('channel:hoge:*'));
    }

    /**
     * @group connected
     */
    public function testCanSendSubscribeAfterPsubscribe()
    {
        $redis = $this->getClient();

        $this->assertSame(array('psubscribe', 'channel:foo:*', 1), $redis->psubscribe('channel:foo:*'));
        $this->assertSame(array('subscribe', 'channel:foo:bar', 2), $redis->subscribe('channel:foo:bar'));
    }

    /**
     * @group connected
     */
    public function testCanSendUnsubscribeAfterPsubscribe()
    {
        $redis = $this->getClient();

        $this->assertSame(array('psubscribe', 'channel:foo:*', 1), $redis->psubscribe('channel:foo:*'));
        $this->assertSame(array('psubscribe', 'channel:hoge:*', 2), $redis->psubscribe('channel:hoge:*'));
        $this->assertSame(array('unsubscribe', 'channel:foo:bar', 2), $redis->unsubscribe('channel:foo:bar'));
    }

    /**
     * @group connected
     */
    public function testCanSendPunsubscribeAfterPsubscribe()
    {
        $redis = $this->getClient();

        $this->assertSame(array('psubscribe', 'channel:foo:*', 1), $redis->psubscribe('channel:foo:*'));
        $this->assertSame(array('psubscribe', 'channel:hoge:*', 2), $redis->psubscribe('channel:hoge:*'));
        $this->assertSame(array('punsubscribe', 'channel:*:*', 2), $redis->punsubscribe('channel:*:*'));
    }

    /**
     * @group connected
     */
    public function testCanSendQuitAfterPsubscribe()
    {
        $redis = $this->getClient();
        $quit = $this->getProfile()->createCommand('quit');

        $this->assertSame(array('subscribe', 'channel1', 1), $redis->subscribe('channel1'));
        $this->assertTrue($redis->executeCommand($quit));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR only (P)SUBSCRIBE / (P)UNSUBSCRIBE / QUIT allowed in this context
     */
    public function testCannotSendOtherCommandsAfterPsubscribe()
    {
        $redis = $this->getClient();

        $redis->psubscribe('channel:*');
        $redis->set('foo', 'bar');
    }
}
