<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Collection\Iterator;

use PredisTestCase;
use Predis\Client;
use Predis\Profile\ServerProfile;

/**
 * @group realm-iterators
 */
class KeyspaceTest extends PredisTestCase
{
    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage The specified server profile does not support the `SCAN` command.
     */
    public function testThrowsExceptionOnInvalidServerProfile()
    {
        $client = $this->getMock('Predis\ClientInterface');

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.0')));

        $iterator = new Keyspace($client);
    }

    /**
     * @group disconnected
     */
    public function testIterationWithNoResults()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->once())
               ->method('scan')
               ->with(0, array())
               ->will($this->returnValue(array(0, array())));

        $iterator = new Keyspace($client);

        $iterator->rewind();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnSingleFetch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->once())
               ->method('scan')
               ->with(0, array())
               ->will($this->returnValue(array(0, array('key:1st', 'key:2nd', 'key:3rd'))));

        $iterator = new Keyspace($client);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:3rd', $iterator->current());
        $this->assertSame(2, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('scan')
               ->with(0, array())
               ->will($this->returnValue(array(2, array('key:1st', 'key:2nd'))));
        $client->expects($this->at(2))
               ->method('scan')
               ->with(2, array())
               ->will($this->returnValue(array(0, array('key:3rd'))));

        $iterator = new Keyspace($client);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:3rd', $iterator->current());
        $this->assertSame(2, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnMultipleFetchesAndHoleInFirstFetch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('scan')
               ->with(0, array())
               ->will($this->returnValue(array(4, array())));
        $client->expects($this->at(2))
               ->method('scan')
               ->with(4, array())
               ->will($this->returnValue(array(0, array('key:1st', 'key:2nd'))));

        $iterator = new Keyspace($client);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnMultipleFetchesAndHoleInMidFetch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('scan')
               ->with(0, array())
               ->will($this->returnValue(array(2, array('key:1st', 'key:2nd'))));
        $client->expects($this->at(2))
               ->method('scan')
               ->with(2, array())
               ->will($this->returnValue(array(5, array())));
        $client->expects($this->at(3))
               ->method('scan')
               ->with(5, array())
               ->will($this->returnValue(array(0, array('key:3rd'))));

        $iterator = new Keyspace($client);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:3rd', $iterator->current());
        $this->assertSame(2, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionMatch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('scan')
               ->with(0, array('MATCH' => 'key:*'))
               ->will($this->returnValue(array(0, array('key:1st', 'key:2nd'))));

        $iterator = new Keyspace($client, 'key:*');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionMatchOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('scan')
               ->with(0, array('MATCH' => 'key:*'))
               ->will($this->returnValue(array(1, array('key:1st'))));
        $client->expects($this->at(2))
               ->method('scan')
               ->with(1, array('MATCH' => 'key:*'))
               ->will($this->returnValue(array(0, array('key:2nd'))));

        $iterator = new Keyspace($client, 'key:*');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionCount()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('scan')
               ->with(0, array('COUNT' => 2))
               ->will($this->returnValue(array(0, array('key:1st', 'key:2nd'))));

        $iterator = new Keyspace($client, null, 2);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionCountOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('scan')
               ->with(0, array('COUNT' => 1))
               ->will($this->returnValue(array(1, array('key:1st'))));
        $client->expects($this->at(2))
               ->method('scan')
               ->with(1, array('COUNT' => 1))
               ->will($this->returnValue(array(0, array('key:2nd'))));

        $iterator = new Keyspace($client, null, 1);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionsMatchAndCount()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('scan')
               ->with(0, array('MATCH' => 'key:*', 'COUNT' => 2))
               ->will($this->returnValue(array(0, array('key:1st', 'key:2nd'))));

        $iterator = new Keyspace($client, 'key:*', 2);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionsMatchAndCountOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('scan')
               ->with(0, array('MATCH' => 'key:*', 'COUNT' => 1))
               ->will($this->returnValue(array(1, array('key:1st'))));
        $client->expects($this->at(2))
               ->method('scan')
               ->with(1, array('MATCH' => 'key:*', 'COUNT' => 1))
               ->will($this->returnValue(array(0, array('key:2nd'))));

        $iterator = new Keyspace($client, 'key:*', 1);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationRewindable()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'scan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->exactly(2))
               ->method('scan')
               ->with(0, array())
               ->will($this->returnValue(array(0, array('key:1st', 'key:2nd'))));

        $iterator = new Keyspace($client);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('key:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame(1, $iterator->key());
        $this->assertSame('key:2nd', $iterator->current());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }
}
