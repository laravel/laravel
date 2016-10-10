<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Connection;

use PredisTestCase;
use Predis\ResponseError;
use Predis\Command\RawCommand;
use Predis\Profile\ServerProfile;

/**
 *
 */
class RedisClusterTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testExposesCommandHashStrategy()
    {
        $cluster = new RedisCluster();
        $this->assertInstanceOf('Predis\Cluster\RedisClusterHashStrategy', $cluster->getCommandHashStrategy());
    }

    /**
     * @group disconnected
     */
    public function testAddingConnectionsToCluster()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);

        $this->assertSame(2, count($cluster));
        $this->assertSame($connection1, $cluster->getConnectionById('127.0.0.1:6379'));
        $this->assertSame($connection2, $cluster->getConnectionById('127.0.0.1:6380'));
    }

    /**
     * @group disconnected
     */
    public function testRemovingConnectionsFromCluster()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection3 = $this->getMockConnection('tcp://127.0.0.1:6371');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);

        $this->assertTrue($cluster->remove($connection1));
        $this->assertFalse($cluster->remove($connection3));
        $this->assertSame(1, count($cluster));
    }

    /**
     * @group disconnected
     */
    public function testRemovingConnectionsFromClusterByAlias()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);

        $this->assertTrue($cluster->removeById('127.0.0.1:6380'));
        $this->assertFalse($cluster->removeById('127.0.0.1:6390'));
        $this->assertSame(1, count($cluster));
    }

    /**
     * @group disconnected
     */
    public function testCountReturnsNumberOfConnectionsInPool()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection3 = $this->getMockConnection('tcp://127.0.0.1:6381');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);
        $cluster->add($connection3);

        $this->assertSame(3, count($cluster));

        $cluster->remove($connection3);

        $this->assertSame(2, count($cluster));
    }

    /**
     * @group disconnected
     */
    public function testConnectPicksRandomConnection()
    {
        $connect1 = false;
        $connect2 = false;

        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection1->expects($this->any())
                    ->method('connect')
                    ->will($this->returnCallback(function () use (&$connect1) {
                        $connect1 = true;
                    }));
        $connection1->expects($this->any())
                    ->method('isConnected')
                    ->will($this->returnCallback(function () use (&$connect1) {
                        return $connect1;
                    }));

        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection2->expects($this->any())
                    ->method('connect')
                    ->will($this->returnCallback(function () use (&$connect2) {
                        $connect2 = true;
                    }));
        $connection2->expects($this->any())
                    ->method('isConnected')
                    ->will($this->returnCallback(function () use (&$connect2) {
                        return $connect2;
                    }));

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);

        $cluster->connect();

        $this->assertTrue($cluster->isConnected());

        if ($connect1) {
            $this->assertTrue($connect1);
            $this->assertFalse($connect2);
        } else {
            $this->assertFalse($connect1);
            $this->assertTrue($connect2);
        }
    }

    /**
     * @group disconnected
     */
    public function testDisconnectForcesAllConnectionsToDisconnect()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection1->expects($this->once())->method('disconnect');

        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection2->expects($this->once())->method('disconnect');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);

        $cluster->disconnect();
    }

    /**
     * @group disconnected
     */
    public function testIsConnectedReturnsTrueIfAtLeastOneConnectionIsOpen()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection1->expects($this->once())
                    ->method('isConnected')
                    ->will($this->returnValue(false));

        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection2->expects($this->once())
                    ->method('isConnected')
                    ->will($this->returnValue(true));

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);

        $this->assertTrue($cluster->isConnected());
    }

    /**
     * @group disconnected
     */
    public function testIsConnectedReturnsFalseIfAllConnectionsAreClosed()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection1->expects($this->once())
                    ->method('isConnected')
                    ->will($this->returnValue(false));

        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection2->expects($this->once())
                    ->method('isConnected')
                    ->will($this->returnValue(false));

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);

        $this->assertFalse($cluster->isConnected());
    }

    /**
     * @group disconnected
     */
    public function testCanReturnAnIteratorForConnections()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);

        $this->assertInstanceOf('Iterator', $iterator = $cluster->getIterator());
        $connections = iterator_to_array($iterator);

        $this->assertSame($connection1, $connections[0]);
        $this->assertSame($connection2, $connections[1]);
    }

    /**
     * @group disconnected
     */
    public function testCanAssignConnectionsToCustomSlots()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection3 = $this->getMockConnection('tcp://127.0.0.1:6381');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);
        $cluster->add($connection3);

        $cluster->setSlots(0, 1364, '127.0.0.1:6379');
        $cluster->setSlots(1365, 2729, '127.0.0.1:6380');
        $cluster->setSlots(2730, 4095, '127.0.0.1:6381');

        $expectedMap = array_merge(
            array_fill(0, 1365, '127.0.0.1:6379'),
            array_fill(1364, 1365, '127.0.0.1:6380'),
            array_fill(2729, 1366, '127.0.0.1:6381')
        );

        $this->assertSame($expectedMap, $cluster->getSlotsMap());
    }

    /**
     * @group disconnected
     */
    public function testAddingConnectionResetsSlotsMap()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');

        $cluster = new RedisCluster();
        $cluster->add($connection1);

        $cluster->setSlots(0, 4095, '127.0.0.1:6379');
        $this->assertSame(array_fill(0, 4096, '127.0.0.1:6379'), $cluster->getSlotsMap());

        $cluster->add($connection2);

        $this->assertEmpty($cluster->getSlotsMap());
    }

    /**
     * @group disconnected
     */
    public function testRemovingConnectionResetsSlotsMap()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);

        $cluster->setSlots(0, 2047, '127.0.0.1:6379');
        $cluster->setSlots(2048, 4095, '127.0.0.1:6380');

        $expectedMap = array_merge(
            array_fill(0, 2048, '127.0.0.1:6379'),
            array_fill(2048, 2048, '127.0.0.1:6380')
        );

        $this->assertSame($expectedMap, $cluster->getSlotsMap());

        $cluster->remove($connection1);
        $this->assertEmpty($cluster->getSlotsMap());
    }

    /**
     * @group disconnected
     */
    public function testCanAssignConnectionsToCustomSlotsFromParameters()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379?slots=0-5460');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380?slots=5461-10921');
        $connection3 = $this->getMockConnection('tcp://127.0.0.1:6381?slots=10922-16383');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);
        $cluster->add($connection3);

        $expectedMap = array_merge(
            array_fill(0, 5461, '127.0.0.1:6379'),
            array_fill(5460, 5461, '127.0.0.1:6380'),
            array_fill(10921, 5462, '127.0.0.1:6381')
        );

        $cluster->buildSlotsMap();

        $this->assertSame($expectedMap, $cluster->getSlotsMap());
    }

    /**
     * @group disconnected
     */
    public function testReturnsCorrectConnectionUsingSlotID()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection3 = $this->getMockConnection('tcp://127.0.0.1:6381');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);
        $cluster->add($connection3);

        $this->assertSame($connection1, $cluster->getConnectionBySlot(0));
        $this->assertSame($connection2, $cluster->getConnectionBySlot(5461));
        $this->assertSame($connection3, $cluster->getConnectionBySlot(10922));

        $cluster->setSlots(5461, 7096, '127.0.0.1:6380');
        $this->assertSame($connection2, $cluster->getConnectionBySlot(5461));
    }

    /**
     * @group disconnected
     */
    public function testReturnsCorrectConnectionUsingCommandInstance()
    {
        $profile = ServerProfile::getDefault();

        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection3 = $this->getMockConnection('tcp://127.0.0.1:6381');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);
        $cluster->add($connection3);

        $set = $profile->createCommand('set', array('node:1001', 'foobar'));
        $get = $profile->createCommand('get', array('node:1001'));
        $this->assertSame($connection1, $cluster->getConnection($set));
        $this->assertSame($connection1, $cluster->getConnection($get));

        $set = $profile->createCommand('set', array('node:1048', 'foobar'));
        $get = $profile->createCommand('get', array('node:1048'));
        $this->assertSame($connection2, $cluster->getConnection($set));
        $this->assertSame($connection2, $cluster->getConnection($get));

        $set = $profile->createCommand('set', array('node:1082', 'foobar'));
        $get = $profile->createCommand('get', array('node:1082'));
        $this->assertSame($connection3, $cluster->getConnection($set));
        $this->assertSame($connection3, $cluster->getConnection($get));
    }

    /**
     * @group disconnected
     */
    public function testWritesCommandToCorrectConnection()
    {
        $command = ServerProfile::getDefault()->createCommand('get', array('node:1001'));

        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection1->expects($this->once())->method('writeCommand')->with($command);

        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection2->expects($this->never())->method('writeCommand');

        $cluster = new RedisCluster();
        $cluster->enableClusterNodes(false);
        $cluster->add($connection1);
        $cluster->add($connection2);

        $cluster->writeCommand($command);
    }

    /**
     * @group disconnected
     */
    public function testReadsCommandFromCorrectConnection()
    {
        $command = ServerProfile::getDefault()->createCommand('get', array('node:1050'));

        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection1->expects($this->never())->method('readResponse');

        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection2->expects($this->once())->method('readResponse')->with($command);

        $cluster = new RedisCluster();
        $cluster->enableClusterNodes(false);
        $cluster->add($connection1);
        $cluster->add($connection2);

        $cluster->readResponse($command);
    }

    /**
     * @group disconnected
     */
    public function testSupportsKeyTags()
    {
        $profile = ServerProfile::getDefault();

        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);

        $set = $profile->createCommand('set', array('{node:1001}:foo', 'foobar'));
        $get = $profile->createCommand('get', array('{node:1001}:foo'));
        $this->assertSame($connection1, $cluster->getConnection($set));
        $this->assertSame($connection1, $cluster->getConnection($get));

        $set = $profile->createCommand('set', array('{node:1001}:bar', 'foobar'));
        $get = $profile->createCommand('get', array('{node:1001}:bar'));
        $this->assertSame($connection1, $cluster->getConnection($set));
        $this->assertSame($connection1, $cluster->getConnection($get));
    }

    /**
     * @group disconnected
     */
    public function testAskResponseWithConnectionInPool()
    {
        $askResponse = new ResponseError('ASK 1970 127.0.0.1:6380');

        $command = ServerProfile::getDefault()->createCommand('get', array('node:1001'));

        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection1->expects($this->exactly(2))
                    ->method('executeCommand')
                    ->with($command)
                    ->will($this->onConsecutiveCalls($askResponse, 'foobar'));

        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection2->expects($this->at(2))
                    ->method('executeCommand')
                    ->with($this->isRedisCommand('ASKING'));
        $connection2->expects($this->at(3))
                    ->method('executeCommand')
                    ->with($command)
                    ->will($this->returnValue('foobar'));

        $factory = $this->getMock('Predis\Connection\ConnectionFactory');
        $factory->expects($this->never())->method('create');

        $cluster = new RedisCluster($factory);
        $cluster->enableClusterNodes(false);
        $cluster->add($connection1);
        $cluster->add($connection2);

        $this->assertSame('foobar', $cluster->executeCommand($command));
        $this->assertSame('foobar', $cluster->executeCommand($command));
        $this->assertSame(2, count($cluster));
    }

    /**
     * @group disconnected
     */
    public function testAskResponseWithConnectionNotInPool()
    {
        $askResponse = new ResponseError('ASK 1970 127.0.0.1:6381');

        $command = ServerProfile::getDefault()->createCommand('get', array('node:1001'));

        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection1->expects($this->exactly(2))
                    ->method('executeCommand')
                    ->with($command)
                    ->will($this->onConsecutiveCalls($askResponse, 'foobar'));

        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection2->expects($this->never())
                    ->method('executeCommand');

        $connection3 = $this->getMockConnection('tcp://127.0.0.1:6381');
        $connection3->expects($this->at(0))
                    ->method('executeCommand')
                    ->with($this->isRedisCommand('ASKING'));
        $connection3->expects($this->at(1))
                    ->method('executeCommand')
                    ->with($command)
                    ->will($this->returnValue('foobar'));

        $factory = $this->getMock('Predis\Connection\ConnectionFactory');
        $factory->expects($this->once())
                ->method('create')
                ->with(array('host' => '127.0.0.1', 'port' => '6381'))
                ->will($this->returnValue($connection3));

        $cluster = new RedisCluster($factory);
        $cluster->enableClusterNodes(false);
        $cluster->add($connection1);
        $cluster->add($connection2);

        $this->assertSame('foobar', $cluster->executeCommand($command));
        $this->assertSame('foobar', $cluster->executeCommand($command));
        $this->assertSame(2, count($cluster));
    }

    /**
     * @group disconnected
     */
    public function testMovedResponseWithConnectionInPool()
    {
        $movedResponse = new ResponseError('MOVED 1970 127.0.0.1:6380');

        $command = ServerProfile::getDefault()->createCommand('get', array('node:1001'));

        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection1->expects($this->exactly(1))
                    ->method('executeCommand')
                    ->with($command)
                    ->will($this->returnValue($movedResponse));

        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection2->expects($this->exactly(2))
                    ->method('executeCommand')
                    ->with($command)
                    ->will($this->onConsecutiveCalls('foobar', 'foobar'));

        $factory = $this->getMock('Predis\Connection\ConnectionFactory');
        $factory->expects($this->never())->method('create');

        $cluster = new RedisCluster($factory);
        $cluster->enableClusterNodes(false);
        $cluster->add($connection1);
        $cluster->add($connection2);

        $this->assertSame('foobar', $cluster->executeCommand($command));
        $this->assertSame('foobar', $cluster->executeCommand($command));
        $this->assertSame(2, count($cluster));
    }

    /**
     * @group disconnected
     */
    public function testMovedResponseWithConnectionNotInPool()
    {
        $movedResponse = new ResponseError('MOVED 1970 127.0.0.1:6381');

        $command = ServerProfile::getDefault()->createCommand('get', array('node:1001'));

        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection1->expects($this->once())
                    ->method('executeCommand')
                    ->with($command)
                    ->will($this->returnValue($movedResponse));

        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection2->expects($this->never())
                    ->method('executeCommand');

        $connection3 = $this->getMockConnection('tcp://127.0.0.1:6381');
        $connection3->expects($this->exactly(2))
                    ->method('executeCommand')
                    ->with($command)
                    ->will($this->onConsecutiveCalls('foobar', 'foobar'));

        $factory = $this->getMock('Predis\Connection\ConnectionFactory');
        $factory->expects($this->once())
                ->method('create')
                ->with(array('host' => '127.0.0.1', 'port' => '6381'))
                ->will($this->returnValue($connection3));

        $cluster = new RedisCluster($factory);
        $cluster->enableClusterNodes(false);
        $cluster->add($connection1);
        $cluster->add($connection2);

        $this->assertSame('foobar', $cluster->executeCommand($command));
        $this->assertSame('foobar', $cluster->executeCommand($command));
        $this->assertSame(3, count($cluster));
    }

    /**
     * @group disconnected
     */
    public function testFetchSlotsMapFromClusterWithClusterSlotsCommand()
    {
        $response = array(
            array(12288, 13311, array('10.1.0.51', 6387), array('10.1.0.52', 6387)),
            array(3072 ,  4095, array('10.1.0.52', 6392), array('10.1.0.51', 6392)),
            array(6144 ,  7167, array('', 6384), array('10.1.0.52', 6384)),
            array(14336, 15359, array('10.1.0.51', 6388), array('10.1.0.52', 6388)),
            array(15360, 16383, array('10.1.0.52', 6398), array('10.1.0.51', 6398)),
            array(1024 ,  2047, array('10.1.0.52', 6391), array('10.1.0.51', 6391)),
            array(11264, 12287, array('10.1.0.52', 6396), array('10.1.0.51', 6396)),
            array( 5120,  6143, array('10.1.0.52', 6393), array('10.1.0.51', 6393)),
            array(    0,  1023, array('10.1.0.51', 6381), array('10.1.0.52', 6381)),
            array(13312, 14335, array('10.1.0.52', 6397), array('10.1.0.51', 6397)),
            array( 4096,  5119, array('10.1.0.51', 6383), array('10.1.0.52', 6383)),
            array( 9216, 10239, array('10.1.0.52', 6395), array('10.1.0.51', 6395)),
            array( 8192,  9215, array('10.1.0.51', 6385), array('10.1.0.52', 6385)),
            array(10240, 11263, array('10.1.0.51', 6386), array('10.1.0.52', 6386)),
            array( 2048,  3071, array('10.1.0.51', 6382), array('10.1.0.52', 6382)),
            array( 7168,  8191, array('10.1.0.52', 6394), array('10.1.0.51', 6394)),
        );

        $command = RawCommand::create('CLUSTER', 'SLOTS');

        $connection1 = $this->getMockConnection('tcp://10.1.0.51:6384');
        $connection1->expects($this->once())
                    ->method('executeCommand')
                    ->with($command)
                    ->will($this->returnValue($response));

        $factory = $this->getMock('Predis\Connection\ConnectionFactory');

        $cluster = new RedisCluster($factory);
        $cluster->add($connection1);

        $cluster->askClusterNodes();

        $this->assertSame($cluster->getConnectionBySlot('6144'), $connection1);
    }

    /**
     * @group disconnected
     */
    public function testAskSlotsMapToRedisClusterOnMovedResponseByDefault()
    {
        $cmdGET = RawCommand::create('GET', 'node:1001');
        $rspMOVED = new ResponseError('MOVED 1970 127.0.0.1:6380');
        $rspSlotsArray = array(
            array(0   ,  8191, array('127.0.0.1', 6379)),
            array(8192, 16383, array('127.0.0.1', 6380)),
        );

        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379');
        $connection1->expects($this->once())
                    ->method('executeCommand')
                    ->with($cmdGET)
                    ->will($this->returnValue($rspMOVED));

        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380');
        $connection2->expects($this->at(0))
                    ->method('executeCommand')
                    ->with($this->isRedisCommand('CLUSTER', array('SLOTS')))
                    ->will($this->returnValue($rspSlotsArray));
        $connection2->expects($this->at(2))
                    ->method('executeCommand')
                    ->with($cmdGET)
                    ->will($this->returnValue('foobar'));

        $factory = $this->getMock('Predis\Connection\ConnectionFactory');
        $factory->expects($this->once())
                ->method('create')
                ->with(array('host' => '127.0.0.1', 'port' => '6380'))
                ->will($this->returnValue($connection2));

        $cluster = new RedisCluster($factory);
        $cluster->add($connection1);

        $this->assertSame('foobar', $cluster->executeCommand($cmdGET));
        $this->assertSame(2, count($cluster));
    }

    /**
     * @group disconnected
     * @expectedException Predis\NotSupportedException
     * @expectedExceptionMessage Cannot use PING with redis-cluster
     */
    public function testThrowsExceptionOnNonSupportedCommand()
    {
        $ping = ServerProfile::getDefault()->createCommand('ping');

        $cluster = new RedisCluster();
        $cluster->add($this->getMockConnection('tcp://127.0.0.1:6379'));

        $cluster->getConnection($ping);
    }

    /**
     * @group disconnected
     */
    public function testCanBeSerialized()
    {
        $connection1 = $this->getMockConnection('tcp://127.0.0.1:6379?slots=0-1364');
        $connection2 = $this->getMockConnection('tcp://127.0.0.1:6380?slots=1365-2729');
        $connection3 = $this->getMockConnection('tcp://127.0.0.1:6381?slots=2730-4095');

        $cluster = new RedisCluster();
        $cluster->add($connection1);
        $cluster->add($connection2);
        $cluster->add($connection3);

        $cluster->buildSlotsMap();

        $unserialized = unserialize(serialize($cluster));

        $this->assertEquals($cluster, $unserialized);
    }

    // ******************************************************************** //
    // ---- HELPER METHODS ------------------------------------------------ //
    // ******************************************************************** //

    /**
     * Returns a base mocked connection from Predis\Connection\SingleConnectionInterface.
     *
     * @param  mixed $parameters Optional parameters.
     * @return mixed
     */
    protected function getMockConnection($parameters = null)
    {
        $connection = $this->getMock('Predis\Connection\SingleConnectionInterface');

        if ($parameters) {
            $parameters = new ConnectionParameters($parameters);
            $hash = "{$parameters->host}:{$parameters->port}";

            $connection->expects($this->any())
                       ->method('getParameters')
                       ->will($this->returnValue($parameters));
            $connection->expects($this->any())
                       ->method('__toString')
                       ->will($this->returnValue($hash));
        }

        return $connection;
    }
}
