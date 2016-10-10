<?php

namespace Elasticsearch\Tests\ConnectionPool;

use Elasticsearch;
use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Mockery as m;

/**
 * Class StaticConnectionPoolTest
 *
 * @category   Tests
 * @package    Elasticsearch
 * @subpackage Tests/StaticConnectionPoolTest
 * @author     Zachary Tong <zachary.tong@elasticsearch.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link       http://elasticsearch.org
 */
class StaticConnectionPoolTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testAddOneHostThenGetConnection()
    {
        $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                          ->shouldReceive('ping')
                          ->andReturn(true)
                          ->getMock()
                          ->shouldReceive('isAlive')
                          ->andReturn(true)
                          ->getMock()
                          ->shouldReceive('markDead')->once()->getMock();

        $connections = array($mockConnection);

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturn($connections[0])
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory');

        $randomizeHosts = false;
        $connectionPool = new Elasticsearch\ConnectionPool\StaticConnectionPool($connections, $selector, $connectionFactory, $randomizeHosts);

        $retConnection = $connectionPool->nextConnection();

        $this->assertEquals($mockConnection, $retConnection);
    }

    public function testAddMultipleHostsThenGetFirst()
    {
        $connections = array();

        foreach (range(1, 10) as $index) {
            $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                              ->shouldReceive('ping')
                              ->andReturn(true)
                              ->getMock()
                              ->shouldReceive('isAlive')
                              ->andReturn(true)
                              ->getMock()
                              ->shouldReceive('markDead')->once()->getMock();

            $connections[] = $mockConnection;
        }

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
            ->shouldReceive('select')
            ->andReturn($connections[0])
            ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory');

        $randomizeHosts = false;
        $connectionPool = new Elasticsearch\ConnectionPool\StaticConnectionPool($connections, $selector, $connectionFactory, $randomizeHosts);

        $retConnection = $connectionPool->nextConnection();

        $this->assertEquals($connections[0], $retConnection);
    }

    /**
     * @expectedException Elasticsearch\Common\Exceptions\NoNodesAvailableException
     */
    public function testAllHostsFailPing()
    {
        $connections = array();

        foreach (range(1, 10) as $index) {
            $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                              ->shouldReceive('ping')
                              ->andReturn(false)
                              ->getMock()
                              ->shouldReceive('isAlive')
                              ->andReturn(false)
                              ->getMock()
                              ->shouldReceive('markDead')->once()->getMock()
                              ->shouldReceive('getPingFailures')->andReturn(0)->once()->getMock()
                              ->shouldReceive('getLastPing')->andReturn(time())->once()->getMock();

            $connections[] = $mockConnection;
        }

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturnValues($connections)
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory');

        $randomizeHosts = false;
        $connectionPool = new Elasticsearch\ConnectionPool\StaticConnectionPool($connections, $selector, $connectionFactory, $randomizeHosts);

        $connectionPool->nextConnection();
    }

    public function testAllExceptLastHostFailPingRevivesInSkip()
    {
        $connections = array();

        foreach (range(1, 9) as $index) {
            $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                              ->shouldReceive('ping')
                              ->andReturn(false)
                              ->getMock()
                              ->shouldReceive('isAlive')
                              ->andReturn(false)
                              ->getMock()
                              ->shouldReceive('markDead')->once()->getMock()
                              ->shouldReceive('getPingFailures')->andReturn(0)->once()->getMock()
                              ->shouldReceive('getLastPing')->andReturn(time())->once()->getMock();

            $connections[] = $mockConnection;
        }

        $goodConnection = m::mock('\Elasticsearch\Connections\Connection')
                          ->shouldReceive('ping')->once()
                          ->andReturn(true)
                          ->getMock()
                          ->shouldReceive('isAlive')->once()
                          ->andReturn(false)
                          ->getMock()
                          ->shouldReceive('markDead')->once()->getMock()
                          ->shouldReceive('getPingFailures')->andReturn(0)->once()->getMock()
                          ->shouldReceive('getLastPing')->andReturn(time())->once()->getMock();

        $connections[] = $goodConnection;

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturnValues($connections)
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory');

        $randomizeHosts = false;
        $connectionPool = new Elasticsearch\ConnectionPool\StaticConnectionPool($connections, $selector, $connectionFactory, $randomizeHosts);

        $ret = $connectionPool->nextConnection();
        $this->assertEquals($goodConnection, $ret);
    }

    public function testAllExceptLastHostFailPingRevivesPreSkip()
    {
        $connections = array();

        foreach (range(1, 9) as $index) {
            $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                              ->shouldReceive('ping')
                              ->andReturn(false)
                              ->getMock()
                              ->shouldReceive('isAlive')
                              ->andReturn(false)
                              ->getMock()
                              ->shouldReceive('markDead')->once()->getMock()
                              ->shouldReceive('getPingFailures')->andReturn(0)->once()->getMock()
                              ->shouldReceive('getLastPing')->andReturn(time())->once()->getMock();

            $connections[] = $mockConnection;
        }

        $goodConnection = m::mock('\Elasticsearch\Connections\Connection')
                          ->shouldReceive('ping')->once()
                          ->andReturn(true)
                          ->getMock()
                          ->shouldReceive('isAlive')->once()
                          ->andReturn(false)
                          ->getMock()
                          ->shouldReceive('markDead')->once()->getMock()
                          ->shouldReceive('getPingFailures')->andReturn(0)->once()->getMock()
                          ->shouldReceive('getLastPing')->andReturn(time()-10000)->once()->getMock();

        $connections[] = $goodConnection;

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturnValues($connections)
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory');

        $randomizeHosts = false;
        $connectionPool = new Elasticsearch\ConnectionPool\StaticConnectionPool($connections, $selector, $connectionFactory, $randomizeHosts);

        $ret = $connectionPool->nextConnection();
        $this->assertEquals($goodConnection, $ret);
    }

    public function testCustomConnectionPoolIT() {
        $clientBuilder = \Elasticsearch\ClientBuilder::create();
        $clientBuilder->setHosts(['localhost:1']);
        $client = $clientBuilder
            ->setRetries(0)
            ->setConnectionPool('\Elasticsearch\ConnectionPool\StaticConnectionPool', [])
            ->build();

        try {
            $client->search([]);
            $this->fail("Should have thrown NoNodesAvailableException");
        } catch (Elasticsearch\Common\Exceptions\NoNodesAvailableException $e) {
            // All good
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
