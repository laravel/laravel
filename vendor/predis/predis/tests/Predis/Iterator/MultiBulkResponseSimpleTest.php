<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Iterator;

use PredisTestCase;
use Predis\Client;

/**
 * @group realm-iterators
 */
class MultiBulkResponseSimpleTest extends PredisTestCase
{
    /**
     * @group connected
     */
    public function testIterableMultibulk()
    {
        $client = $this->getClient();
        $client->rpush('metavars', 'foo', 'hoge', 'lol');

        $this->assertInstanceOf('Iterator', $iterator = $client->lrange('metavars', 0, -1));
        $this->assertInstanceOf('Predis\Iterator\MultiBulkResponseSimple', $iterator);
        $this->assertTrue($iterator->valid());
        $this->assertSame(3, $iterator->count());

        $this->assertSame('foo', $iterator->current());
        $this->assertSame(1, $iterator->next());
        $this->assertTrue($iterator->valid());

        $this->assertSame('hoge', $iterator->current());
        $this->assertSame(2, $iterator->next());
        $this->assertTrue($iterator->valid());

        $this->assertSame('lol', $iterator->current());
        $this->assertSame(3, $iterator->next());
        $this->assertFalse($iterator->valid());

        $this->assertTrue($client->ping());
    }

    /**
     * @group connected
     */
    public function testIterableMultibulkCanBeWrappedAsTupleIterator()
    {
        $client = $this->getClient();
        $client->mset('foo', 'bar', 'hoge', 'piyo');

        $this->assertInstanceOf('Predis\Iterator\MultiBulkResponseSimple', $iterator = $client->mget('foo', 'bar'));
        $this->assertInstanceOf('Predis\Iterator\MultiBulkResponseTuple', $iterator->asTuple());
    }

    /**
     * @group connected
     */
    public function testSyncWithFalseConsumesReplyFromUnderlyingConnection()
    {
        $client = $this->getClient();
        $client->rpush('metavars', 'foo', 'hoge', 'lol');

        $iterator = $client->lrange('metavars', 0, -1);
        $iterator->sync(false);

        $this->assertTrue($client->isConnected());
        $this->assertTrue($client->ping());
    }

    /**
     * @group connected
     */
    public function testSyncWithTrueDropsUnderlyingConnection()
    {
        $client = $this->getClient();
        $client->rpush('metavars', 'foo', 'hoge', 'lol');

        $iterator = $client->lrange('metavars', 0, -1);
        $iterator->sync(true);

        $this->assertFalse($client->isConnected());
        $this->assertTrue($client->ping());
    }

    /**
     * @group connected
     */
    public function testGarbageCollectorDropsUnderlyingConnection()
    {
        $client = $this->getClient();
        $client->rpush('metavars', 'foo', 'hoge', 'lol');

        $iterator = $client->lrange('metavars', 0, -1);

        unset($iterator);

        $this->assertFalse($client->isConnected());
        $this->assertTrue($client->ping());
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Returns a new client instance.
     *
     * @return Client
     */
    protected function getClient()
    {
        $parameters = $this->getParametersArray(array(
            'iterable_multibulk' => true,
            'read_write_timeout' => 2,
        ));

        $client = $this->createClient($parameters);

        return $client;
    }
}
