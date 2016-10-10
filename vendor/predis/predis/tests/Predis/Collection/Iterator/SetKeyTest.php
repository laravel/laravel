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
class SetKeyTest extends PredisTestCase
{
    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage The specified server profile does not support the `SSCAN` command.
     */
    public function testThrowsExceptionOnInvalidServerProfile()
    {
        $client = $this->getMock('Predis\ClientInterface');

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.0')));

        $iterator = new SetKey($client, 'key:set');
    }

    /**
     * @group disconnected
     */
    public function testIterationWithNoResults()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->once())
               ->method('sscan')
               ->with('key:set', 0, array())
               ->will($this->returnValue(array(0, array())));

        $iterator = new SetKey($client, 'key:set');

        $iterator->rewind();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnSingleFetch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->once())
               ->method('sscan')
               ->with('key:set', 0, array())
               ->will($this->returnValue(array(0, array('member:1st', 'member:2nd', 'member:3rd'))));

        $iterator = new SetKey($client, 'key:set');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:3rd', $iterator->current());
        $this->assertSame(2, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('sscan')
               ->with('key:set', 0, array())
               ->will($this->returnValue(array(2, array('member:1st', 'member:2nd'))));
        $client->expects($this->at(2))
               ->method('sscan')
               ->with('key:set', 2, array())
               ->will($this->returnValue(array(0, array('member:3rd'))));

        $iterator = new SetKey($client, 'key:set');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:3rd', $iterator->current());
        $this->assertSame(2, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnMultipleFetchesAndHoleInFirstFetch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('sscan')
               ->with('key:set', 0, array())
               ->will($this->returnValue(array(4, array())));
        $client->expects($this->at(2))
               ->method('sscan')
               ->with('key:set', 4, array())
               ->will($this->returnValue(array(0, array('member:1st', 'member:2nd'))));

        $iterator = new SetKey($client, 'key:set');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnMultipleFetchesAndHoleInMidFetch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('sscan')
               ->with('key:set', 0, array())
               ->will($this->returnValue(array(2, array('member:1st', 'member:2nd'))));
        $client->expects($this->at(2))
               ->method('sscan')
               ->with('key:set', 2, array())
               ->will($this->returnValue(array(5, array())));
        $client->expects($this->at(3))
               ->method('sscan')
               ->with('key:set', 5, array())
               ->will($this->returnValue(array(0, array('member:3rd'))));

        $iterator = new SetKey($client, 'key:set');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:3rd', $iterator->current());
        $this->assertSame(2, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionMatch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('sscan')
               ->with('key:set', 0, array('MATCH' => 'member:*'))
               ->will($this->returnValue(array(0, array('member:1st', 'member:2nd'))));

        $iterator = new SetKey($client, 'key:set', 'member:*');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionMatchOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('sscan')
               ->with('key:set', 0, array('MATCH' => 'member:*'))
               ->will($this->returnValue(array(1, array('member:1st'))));
        $client->expects($this->at(2))
               ->method('sscan')
               ->with('key:set', 1, array('MATCH' => 'member:*'))
               ->will($this->returnValue(array(0, array('member:2nd'))));

        $iterator = new SetKey($client, 'key:set', 'member:*');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionCount()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('sscan')
               ->with('key:set', 0, array('COUNT' => 2))
               ->will($this->returnValue(array(0, array('member:1st', 'member:2nd'))));

        $iterator = new SetKey($client, 'key:set', null, 2);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionCountOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('sscan')
               ->with('key:set', 0, array('COUNT' => 1))
               ->will($this->returnValue(array(1, array('member:1st'))));
        $client->expects($this->at(2))
               ->method('sscan')
               ->with('key:set', 1, array('COUNT' => 1))
               ->will($this->returnValue(array(0, array('member:2nd'))));

        $iterator = new SetKey($client, 'key:set', null, 1);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionsMatchAndCount()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('sscan')
               ->with('key:set', 0, array('MATCH' => 'member:*', 'COUNT' => 2))
               ->will($this->returnValue(array(0, array('member:1st', 'member:2nd'))));

        $iterator = new SetKey($client, 'key:set', 'member:*', 2);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionsMatchAndCountOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->at(1))
               ->method('sscan')
               ->with('key:set', 0, array('MATCH' => 'member:*', 'COUNT' => 1))
               ->will($this->returnValue(array(1, array('member:1st'))));
        $client->expects($this->at(2))
               ->method('sscan')
               ->with('key:set', 1, array('MATCH' => 'member:*', 'COUNT' => 1))
               ->will($this->returnValue(array(0, array('member:2nd'))));

        $iterator = new SetKey($client, 'key:set', 'member:*', 1);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationRewindable()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'sscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::get('2.8')));
        $client->expects($this->exactly(2))
               ->method('sscan')
               ->with('key:set', 0, array())
               ->will($this->returnValue(array(0, array('member:1st', 'member:2nd'))));

        $iterator = new SetKey($client, 'key:set');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:1st', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('member:2nd', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }
}
