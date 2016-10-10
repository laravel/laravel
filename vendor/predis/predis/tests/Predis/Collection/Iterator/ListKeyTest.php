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
class ListKeyTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testIterationWithNoResults()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'lrange'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::getDefault()));
        $client->expects($this->once())
               ->method('lrange')
               ->with('key:list', 0, 9)
               ->will($this->returnValue(array()));

        $iterator = new ListKey($client, 'key:list');

        $iterator->rewind();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnSingleFetch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'lrange'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::getDefault()));
        $client->expects($this->once())
               ->method('lrange')
               ->with('key:list', 0, 9)
               ->will($this->returnValue(array('item:1', 'item:2', 'item:3')));

        $iterator = new ListKey($client, 'key:list');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('item:1', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('item:2', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('item:3', $iterator->current());
        $this->assertSame(2, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'lrange'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::getDefault()));
        $client->expects($this->at(1))
               ->method('lrange')
               ->with('key:list', 0, 9)
               ->will($this->returnValue(array(
                    'item:1', 'item:2', 'item:3', 'item:4', 'item:5', 'item:6', 'item:7', 'item:8', 'item:9', 'item:10'
               )));
        $client->expects($this->at(2))
               ->method('lrange')
               ->with('key:list', 10, 19)
               ->will($this->returnValue(array('item:11', 'item:12')));

        $iterator = new ListKey($client, 'key:list');

        for ($i = 1, $iterator->rewind(); $i <= 12; $i++, $iterator->next()) {
            $this->assertTrue($iterator->valid());
            $this->assertSame("item:$i", $iterator->current());
            $this->assertSame($i - 1, $iterator->key());
        }

        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The $count argument must be a positive integer.
     */
    public function testThrowsExceptionOnConstructorWithNonIntegerCountParameter()
    {
        $client = $this->getMock('Predis\ClientInterface');
        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::getDefault()));

        $iterator = new ListKey($client, 'key:list', 'wrong');
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The $count argument must be a positive integer.
     */
    public function testThrowsExceptionOnConstructorWithNegativeCountParameter()
    {
        $client = $this->getMock('Predis\ClientInterface');
        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::getDefault()));

        $iterator = new ListKey($client, 'key:list', 'wrong');
    }

    /**
     * @group disconnected
     */
    public function testIterationWithCountParameter()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'lrange'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::getDefault()));
        $client->expects($this->at(1))
               ->method('lrange')
               ->with('key:list', 0, 4)
               ->will($this->returnValue(array('item:1', 'item:2')));

        $iterator = new ListKey($client, 'key:list', 5);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('item:1', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('item:2', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithCountParameterOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'lrange'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::getDefault()));
        $client->expects($this->at(1))
               ->method('lrange')
               ->with('key:list', 0, 1)
               ->will($this->returnValue(array('item:1', 'item:2')));
        $client->expects($this->at(2))
               ->method('lrange')
               ->with('key:list', 2, 3)
               ->will($this->returnValue(array('item:3')));

        $iterator = new ListKey($client, 'key:list', 2);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('item:1', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('item:2', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('item:3', $iterator->current());
        $this->assertSame(2, $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationRewindable()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'lrange'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(ServerProfile::getDefault()));
        $client->expects($this->exactly(2))
               ->method('lrange')
               ->with('key:list', 0, 9)
               ->will($this->returnValue(array('item:1', 'item:2')));

        $iterator = new ListKey($client, 'key:list');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('item:1', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('item:1', $iterator->current());
        $this->assertSame(0, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame(1, $iterator->key());
        $this->assertSame('item:2', $iterator->current());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }
}
