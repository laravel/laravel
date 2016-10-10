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
class PubSubSubscribeTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\PubSubSubscribe';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SUBSCRIBE';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('channel:foo', 'channel:bar');
        $expected = array('channel:foo', 'channel:bar');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsAsSingleArray()
    {
        $arguments = array(array('channel:foo', 'channel:bar'));
        $expected = array('channel:foo', 'channel:bar');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('subscribe', 'channel', 1);
        $expected = array('subscribe', 'channel', 1);

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }

    /**
     * @group disconnected
     */
    public function testPrefixKeys()
    {
        $arguments = array(array('channel:foo', 'channel:bar'));
        $expected = array('prefix:channel:foo', 'prefix:channel:bar');

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
    public function testReturnsTheFirstSubscribedChannelDetails()
    {
        $redis = $this->getClient();

        $this->assertSame(array('subscribe', 'channel', 1), $redis->subscribe('channel'));
    }

    /**
     * @group connected
     */
    public function testCanSendSubscribeAfterSubscribe()
    {
        $redis = $this->getClient();

        $this->assertSame(array('subscribe', 'channel:foo', 1), $redis->subscribe('channel:foo'));
        $this->assertSame(array('subscribe', 'channel:bar', 2), $redis->subscribe('channel:bar'));
    }

    /**
     * @group connected
     */
    public function testCanSendPsubscribeAfterSubscribe()
    {
        $redis = $this->getClient();

        $this->assertSame(array('subscribe', 'channel:foo', 1), $redis->subscribe('channel:foo'));
        $this->assertSame(array('psubscribe', 'channel:*', 2), $redis->psubscribe('channel:*'));
    }

    /**
     * @group connected
     */
    public function testCanSendUnsubscribeAfterSubscribe()
    {
        $redis = $this->getClient();

        $this->assertSame(array('subscribe', 'channel:foo', 1), $redis->subscribe('channel:foo'));
        $this->assertSame(array('subscribe', 'channel:bar', 2), $redis->subscribe('channel:bar'));
        $this->assertSame(array('unsubscribe', 'channel:foo', 1), $redis->unsubscribe('channel:foo'));
    }

    /**
     * @group connected
     */
    public function testCanSendPunsubscribeAfterSubscribe()
    {
        $redis = $this->getClient();

        $this->assertSame(array('subscribe', 'channel:foo', 1), $redis->subscribe('channel:foo'));
        $this->assertSame(array('subscribe', 'channel:bar', 2), $redis->subscribe('channel:bar'));
        $this->assertSame(array('punsubscribe', 'channel:*', 2), $redis->punsubscribe('channel:*'));
    }

    /**
     * @group connected
     */
    public function testCanSendQuitAfterSubscribe()
    {
        $redis = $this->getClient();
        $quit = $this->getProfile()->createCommand('quit');

        $this->assertSame(array('subscribe', 'channel:foo', 1), $redis->subscribe('channel:foo'));
        $this->assertTrue($redis->executeCommand($quit));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR only (P)SUBSCRIBE / (P)UNSUBSCRIBE / QUIT allowed in this context
     */
    public function testCannotSendOtherCommandsAfterSubscribe()
    {
        $redis = $this->getClient();

        $redis->subscribe('channel:foo');
        $redis->set('foo', 'bar');
    }
}
