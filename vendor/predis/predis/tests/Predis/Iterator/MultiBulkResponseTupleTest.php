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
class MultiBulkResponseTupleTest extends PredisTestCase
{
    /**
     * @group disconnected
     * @expectedException RuntimeException
     * @expectedExceptionMessage Cannot initialize a tuple iterator with an already initiated iterator
     */
    public function testInitiatedMultiBulkIteratorsAreNotValid()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $iterator = new MultiBulkResponseSimple($connection, 2);
        $iterator->next();

        new MultiBulkResponseTuple($iterator);
    }

    /**
     * @group disconnected
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Invalid reply size for a tuple iterator [3]
     */
    public function testMultiBulkWithOddSizesAreInvalid()
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');
        $iterator = new MultiBulkResponseSimple($connection, 3);

        new MultiBulkResponseTuple($iterator);
    }

    /**
     * @group connected
     */
    public function testIterableMultibulk()
    {
        $client = $this->getClient();
        $client->zadd('metavars', 1, 'foo', 2, 'hoge', 3, 'lol');

        $this->assertInstanceOf('OuterIterator', $iterator = $client->zrange('metavars', 0, -1, 'withscores')->asTuple());
        $this->assertInstanceOf('Predis\Iterator\MultiBulkResponseTuple', $iterator);
        $this->assertInstanceOf('Predis\Iterator\MultiBulkResponseSimple', $iterator->getInnerIterator());
        $this->assertTrue($iterator->valid());
        $this->assertSame(3, $iterator->count());

        $this->assertSame(array('foo', '1'), $iterator->current());
        $this->assertSame(1, $iterator->next());
        $this->assertTrue($iterator->valid());

        $this->assertSame(array('hoge', '2'), $iterator->current());
        $this->assertSame(2, $iterator->next());
        $this->assertTrue($iterator->valid());

        $this->assertSame(array('lol', '3'), $iterator->current());
        $this->assertSame(3, $iterator->next());
        $this->assertFalse($iterator->valid());

        $this->assertTrue($client->ping());
    }

    /**
     * @group connected
     */
    public function testGarbageCollectorDropsUnderlyingConnection()
    {
        $client = $this->getClient();
        $client->zadd('metavars', 1, 'foo', 2, 'hoge', 3, 'lol');

        $iterator = $client->zrange('metavars', 0, -1, 'withscores')->asTuple();

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
