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
 * @group realm-transaction
 */
class TransactionWatchTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\TransactionWatch';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'WATCH';
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
        $this->assertTrue($this->getCommand()->parseResponse(true));
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
    public function testAbortsTransactionOnExternalWriteOperations()
    {
        $redis1 = $this->getClient();
        $redis2 = $this->getClient();

        $redis1->mset('foo', 'bar', 'hoge', 'piyo');

        $this->assertTrue($redis1->watch('foo', 'hoge'));
        $this->assertTrue($redis1->multi());
        $this->assertInstanceOf('Predis\ResponseQueued', $redis1->get('foo'));
        $this->assertTrue($redis2->set('foo', 'hijacked'));
        $this->assertNull($redis1->exec());
        $this->assertSame('hijacked', $redis1->get('foo'));
    }

    /**
     * @group connected
     */
    public function testCanWatchNotYetExistingKeys()
    {
        $redis1 = $this->getClient();
        $redis2 = $this->getClient();

        $this->assertTrue($redis1->watch('foo'));
        $this->assertTrue($redis1->multi());
        $this->assertInstanceOf('Predis\ResponseQueued', $redis1->set('foo', 'bar'));
        $this->assertTrue($redis2->set('foo', 'hijacked'));
        $this->assertNull($redis1->exec());
        $this->assertSame('hijacked', $redis1->get('foo'));
    }

    /**
     * @group connected
     * @expectedException Predis\ServerException
     * @expectedExceptionMessage ERR WATCH inside MULTI is not allowed
     */
    public function testThrowsExceptionWhenCallingInsideTransaction()
    {
        $redis = $this->getClient();

        $redis->multi();
        $redis->watch('foo');
    }
}
