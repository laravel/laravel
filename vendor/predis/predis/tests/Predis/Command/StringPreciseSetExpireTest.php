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
class StringPreciseSetExpireTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\StringPreciseSetExpire';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'PSETEX';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key', 10, 'hello');
        $expected = array('key', 10, 'hello');

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
        $arguments = array('key', 10, 'hello');
        $expected = array('prefix:key', 10, 'hello');

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
    public function testCreatesNewKeyAndSetsTTL()
    {
        $redis = $this->getClient();

        $this->assertTrue($redis->psetex('foo', 10000, 'bar'));
        $this->assertTrue($redis->exists('foo'));
        $this->assertEquals(10, $redis->ttl('foo'));
    }

    /**
     * @group connected
     * @group slow
     */
    public function testKeyExpiresAfterTTL()
    {
        $redis = $this->getClient();

        $redis->psetex('foo', 50, 'bar');
        $this->sleep(0.5);
        $this->assertFalse($redis->exists('foo'));;
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR value is not an integer or out of range
     */
    public function testThrowsExceptionOnNonIntegerTTL()
    {
        $this->getClient()->psetex('foo', 2.5, 'bar');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR invalid expire time in SETEX
     * @todo Should not Redis return PSETEX instead of SETEX here?
     */
    public function testThrowsExceptionOnZeroTTL()
    {
        $this->getClient()->psetex('foo', 0, 'bar');
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR invalid expire time in SETEX
     * @todo Should not Redis return PSETEX instead of SETEX here?
     */
    public function testThrowsExceptionOnNegativeTTL()
    {
        $this->getClient()->psetex('foo', -10000, 'bar');
    }
}
