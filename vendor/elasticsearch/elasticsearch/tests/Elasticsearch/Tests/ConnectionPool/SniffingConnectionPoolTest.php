<?php

use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use Elasticsearch\ConnectionPool\SniffingConnectionPool;
use Mockery as m;

/**
 * Class SniffingConnectionPoolTest
 *
 * @category   Tests
 * @package    Elasticsearch
 * @subpackage Tests/SniffingConnectionPoolTest
 * @author     Zachary Tong <zachary.tong@elasticsearch.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link       http://elasticsearch.org
 */
class SniffingConnectionPoolTest extends \PHPUnit_Framework_TestCase
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
                          ->getMock();

        $connections = array($mockConnection);

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturn($connections[0])
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory');

        $connectionPoolParams = array('randomizeHosts' => false);
        $connectionPool = new SniffingConnectionPool($connections, $selector, $connectionFactory, $connectionPoolParams);

        $retConnection = $connectionPool->nextConnection();

        $this->assertEquals($mockConnection, $retConnection);
    }

    public function testAddOneHostAndTriggerSniff()
    {
        $clusterState = json_decode('{"ok":true,"cluster_name":"elasticsearch_zach","nodes":{"Bl2ihSr7TcuUHxhu1GA_YQ":{"name":"Vesta","transport_address":"inet[/192.168.1.119:9300]","hostname":"zach-ThinkPad-W530","version":"0.90.5","http_address":"inet[/192.168.1.119:9200]"}}}', true);

        $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                          ->shouldReceive('ping')->andReturn(true)->getMock()
                          ->shouldReceive('isAlive')->andReturn(true)->getMock()
                          ->shouldReceive('getTransportSchema')->once()->andReturn('http')->getMock()
                          ->shouldReceive('sniff')->once()->andReturn($clusterState)->getMock();

        $connections = array($mockConnection);
        $mockNewConnection = m::mock('\Elasticsearch\Connections\Connection')
                             ->shouldReceive('isAlive')->andReturn(true)->getMock();

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')->twice()
                    ->andReturn($mockNewConnection)
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory')
                    ->shouldReceive('create')->with(array('host' => '192.168.1.119', 'port' => 9200))->andReturn($mockNewConnection)->getMock();

        $connectionPoolParams = array(
            'randomizeHosts' => false,
            'sniffingInterval'  => -1
        );
        $connectionPool = new SniffingConnectionPool($connections, $selector, $connectionFactory, $connectionPoolParams);

        $retConnection = $connectionPool->nextConnection();

        $this->assertEquals($mockNewConnection, $retConnection);
    }

    public function testAddOneHostAndForceNext()
    {
        $clusterState = json_decode('{"ok":true,"cluster_name":"elasticsearch_zach","nodes":{"Bl2ihSr7TcuUHxhu1GA_YQ":{"name":"Vesta","transport_address":"inet[/192.168.1.119:9300]","hostname":"zach-ThinkPad-W530","version":"0.90.5","http_address":"inet[/192.168.1.119:9200]"}}}', true);

        $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                          ->shouldReceive('ping')->andReturn(true)->getMock()
                          ->shouldReceive('isAlive')->andReturn(true)->getMock()
                          ->shouldReceive('getTransportSchema')->once()->andReturn('http')->getMock()
                          ->shouldReceive('sniff')->once()->andReturn($clusterState)->getMock();

        $connections = array($mockConnection);
        $mockNewConnection = m::mock('\Elasticsearch\Connections\Connection')
                             ->shouldReceive('isAlive')->andReturn(true)->getMock();

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')->once()->andReturn($mockConnection)->getMock()
                    ->shouldReceive('select')->once()->andReturn($mockNewConnection)->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory')
                             ->shouldReceive('create')->with(array('host' => '192.168.1.119', 'port' => 9200))->andReturn($mockNewConnection)->getMock();

        $connectionPoolParams = array(
            'randomizeHosts' => false
        );
        $connectionPool = new SniffingConnectionPool($connections, $selector, $connectionFactory, $connectionPoolParams);

        $retConnection = $connectionPool->nextConnection(true);

        $this->assertEquals($mockNewConnection, $retConnection);
    }

    public function testAddTenNodesThenGetConnection()
    {
        $connections = array();

        foreach (range(1, 10) as $index) {
            $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                              ->shouldReceive('ping')
                              ->andReturn(true)
                              ->getMock()
                              ->shouldReceive('isAlive')
                              ->andReturn(true)
                              ->getMock();

            $connections[] = $mockConnection;
        }

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturn($connections[0])
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory');

        $connectionPoolParams = array('randomizeHosts' => false);
        $connectionPool = new SniffingConnectionPool($connections, $selector, $connectionFactory, $connectionPoolParams);

        $retConnection = $connectionPool->nextConnection();

        $this->assertEquals($connections[0], $retConnection);
    }

    public function testAddTenNodesTimeoutAllButLast()
    {
        $connections = array();

        foreach (range(1, 9) as $index) {
            $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                              ->shouldReceive('ping')
                              ->andReturn(false)
                              ->getMock()
                              ->shouldReceive('isAlive')
                              ->andReturn(false)
                              ->getMock();

            $connections[] = $mockConnection;
        }

        $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                          ->shouldReceive('ping')
                          ->andReturn(true)
                          ->getMock()
                          ->shouldReceive('isAlive')
                          ->andReturn(true)
                          ->getMock();

        $connections[] = $mockConnection;

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturnValues($connections)
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory');

        $connectionPoolParams = array('randomizeHosts' => false);
        $connectionPool = new SniffingConnectionPool($connections, $selector, $connectionFactory, $connectionPoolParams);

        $retConnection = $connectionPool->nextConnection();

        $this->assertEquals($connections[9], $retConnection);
    }

    /**
     * @expectedException Elasticsearch\Common\Exceptions\NoNodesAvailableException
     */
    public function testAddTenNodesAllTimeout()
    {
        $connections = array();

        foreach (range(1, 10) as $index) {
            $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                              ->shouldReceive('ping')
                              ->andReturn(false)
                              ->getMock()
                              ->shouldReceive('isAlive')
                              ->andReturn(false)
                              ->getMock();

            $connections[] = $mockConnection;
        }

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturnValues($connections)
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory');

        $connectionPoolParams = array('randomizeHosts' => false);
        $connectionPool = new SniffingConnectionPool($connections, $selector, $connectionFactory, $connectionPoolParams);

        $retConnection = $connectionPool->nextConnection();
    }

    public function testAddOneHostSniffTwo()
    {
        $clusterState = json_decode('{"ok":true,"cluster_name":"elasticsearch_zach","nodes":{"node1":{"name":"Vesta","transport_address":"inet[/192.168.1.119:9300]","hostname":"zach-ThinkPad-W530","version":"0.90.5","http_address":"inet[/192.168.1.119:9200]"}, "node2":{"name":"Vesta","transport_address":"inet[/192.168.1.119:9301]","hostname":"zach-ThinkPad-W530","version":"0.90.5","http_address":"inet[/192.168.1.119:9201]"}}}', true);

        $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                          ->shouldReceive('ping')->andReturn(true)->getMock()
                          ->shouldReceive('isAlive')->andReturn(true)->getMock()
                          ->shouldReceive('getTransportSchema')->twice()->andReturn('http')->getMock()
                          ->shouldReceive('sniff')->twice()->andReturn($clusterState)->getMock();

        $connections = array($mockConnection);

        $newConnections = array();
        $newConnections[] = m::mock('\Elasticsearch\Connections\Connection')
                             ->shouldReceive('isAlive')->andReturn(true)->getMock();

        $newConnections[] = m::mock('\Elasticsearch\Connections\Connection')
                             ->shouldReceive('isAlive')->andReturn(true)->getMock();

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturnValues(array(        //selects provided node first, then the new cluster list
                            $mockConnection,
                            $newConnections[0],
                            $newConnections[1]
                    ))
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory')
                             ->shouldReceive('create')->with(array('host' => '192.168.1.119', 'port' => 9200))->andReturn($newConnections[0])->getMock()
                             ->shouldReceive('create')->with(array('host' => '192.168.1.119', 'port' => 9201))->andReturn($newConnections[1])->getMock();

        $connectionPoolParams = array(
            'randomizeHosts' => false,
            'sniffingInterval'  => -1
        );
        $connectionPool = new SniffingConnectionPool($connections, $selector, $connectionFactory, $connectionPoolParams);

        $retConnection = $connectionPool->nextConnection();
        $this->assertEquals($newConnections[0], $retConnection);

        $retConnection = $connectionPool->nextConnection();
        $this->assertEquals($newConnections[1], $retConnection);
    }

    /**
     * @expectedException Elasticsearch\Common\Exceptions\NoNodesAvailableException
     */
    public function testAddSeed_SniffTwo_TimeoutTwo()
    {
        $clusterState = json_decode('{"ok":true,"cluster_name":"elasticsearch_zach","nodes":{"node1":{"name":"Vesta","transport_address":"inet[/192.168.1.119:9300]","hostname":"zach-ThinkPad-W530","version":"0.90.5","http_address":"inet[/192.168.1.119:9200]"}, "node2":{"name":"Vesta","transport_address":"inet[/192.168.1.119:9301]","hostname":"zach-ThinkPad-W530","version":"0.90.5","http_address":"inet[/192.168.1.119:9201]"}}}', true);

        $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                          ->shouldReceive('ping')->andReturn(true)->getMock()
                          ->shouldReceive('isAlive')->andReturn(true)->getMock()
                          ->shouldReceive('getTransportSchema')->once()->andReturn('http')->getMock()
                          ->shouldReceive('sniff')->once()->andReturn($clusterState)->getMock();

        $connections = array($mockConnection);

        $newConnections = array();
        $newConnections[] = m::mock('\Elasticsearch\Connections\Connection')
                            ->shouldReceive('isAlive')->andReturn(false)->getMock()
                            ->shouldReceive('ping')->andReturn(false)->getMock();

        $newConnections[] = m::mock('\Elasticsearch\Connections\Connection')
                            ->shouldReceive('isAlive')->andReturn(false)->getMock()
                            ->shouldReceive('ping')->andReturn(false)->getMock();

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturnValues(array(        //selects provided node first, then the new cluster list
                    $mockConnection,
                    $newConnections[0],
                    $newConnections[1]
                ))
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory')
                             ->shouldReceive('create')->with(array('host' => '192.168.1.119', 'port' => 9200))->andReturn($newConnections[0])->getMock()
                             ->shouldReceive('create')->with(array('host' => '192.168.1.119', 'port' => 9201))->andReturn($newConnections[1])->getMock();

        $connectionPoolParams = array(
            'randomizeHosts' => false,
            'sniffingInterval'  => -1
        );
        $connectionPool = new SniffingConnectionPool($connections, $selector, $connectionFactory, $connectionPoolParams);

        $retConnection = $connectionPool->nextConnection();
        $this->assertEquals($mockConnection, $retConnection);
    }

    public function testTen_TimeoutNine_SniffTenth_AddTwoAlive()
    {
        $clusterState = json_decode('{"ok":true,"cluster_name":"elasticsearch_zach","nodes":{"node1":{"name":"Vesta","transport_address":"inet[/192.168.1.119:9300]","hostname":"zach-ThinkPad-W530","version":"0.90.5","http_address":"inet[/192.168.1.119:9200]"}, "node2":{"name":"Vesta","transport_address":"inet[/192.168.1.119:9301]","hostname":"zach-ThinkPad-W530","version":"0.90.5","http_address":"inet[/192.168.1.119:9201]"}}}', true);

        $connections = array();

        foreach (range(1, 10) as $index) {
            $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                              ->shouldReceive('ping')->andReturn(false)->getMock()
                              ->shouldReceive('isAlive')->andReturn(true)->getMock()
                              ->shouldReceive('sniff')->andThrow('Elasticsearch\Common\Exceptions\Curl\OperationTimeoutException')->getMock();

            $connections[] = $mockConnection;
        }

        $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                          ->shouldReceive('ping')->andReturn(true)->getMock()
                          ->shouldReceive('isAlive')->andReturn(true)->getMock()
                          ->shouldReceive('sniff')->andReturn($clusterState)->getMock()
                          ->shouldReceive('getTransportSchema')->twice()->andReturn('http')->getMock();

        $connections[] = $mockConnection;

        $newConnections = $connections;
        $newConnections[] = m::mock('\Elasticsearch\Connections\Connection')
                            ->shouldReceive('isAlive')->andReturn(true)->getMock()
                            ->shouldReceive('ping')->andReturn(true)->getMock();

        $newConnections[] = m::mock('\Elasticsearch\Connections\Connection')
                            ->shouldReceive('isAlive')->andReturn(true)->getMock()
                            ->shouldReceive('ping')->andReturn(true)->getMock();

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturnValues($newConnections)
                    ->getMock();

        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory')
                             ->shouldReceive('create')->with(array('host' => '192.168.1.119', 'port' => 9200))->andReturn($newConnections[10])->getMock()
                             ->shouldReceive('create')->with(array('host' => '192.168.1.119', 'port' => 9201))->andReturn($newConnections[11])->getMock();

        $connectionPoolParams = array(
            'randomizeHosts' => false,
            'sniffingInterval'  => -1
        );
        $connectionPool = new SniffingConnectionPool($connections, $selector, $connectionFactory, $connectionPoolParams);

        $retConnection = $connectionPool->nextConnection();
        $this->assertEquals($newConnections[11], $retConnection);

        $retConnection = $connectionPool->nextConnection();
        $this->assertEquals($newConnections[12], $retConnection);
    }

    /**
     * @expectedException Elasticsearch\Common\Exceptions\NoNodesAvailableException
     */
    public function testTen_TimeoutNine_SniffTenth_AddTwoDead_TimeoutEveryone()
    {
        $clusterState = json_decode('{"ok":true,"cluster_name":"elasticsearch_zach","nodes":{"node1":{"name":"Vesta","transport_address":"inet[/192.168.1.119:9300]","hostname":"zach-ThinkPad-W530","version":"0.90.5","http_address":"inet[/192.168.1.119:9200]"}, "node2":{"name":"Vesta","transport_address":"inet[/192.168.1.119:9301]","hostname":"zach-ThinkPad-W530","version":"0.90.5","http_address":"inet[/192.168.1.119:9201]"}}}', true);

        $connections = array();

        foreach (range(1, 10) as $index) {
            $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                              ->shouldReceive('ping')->andReturn(false)->getMock()
                              ->shouldReceive('isAlive')->andReturn(true)->getMock()
                              ->shouldReceive('sniff')->andThrow('Elasticsearch\Common\Exceptions\Curl\OperationTimeoutException')->getMock();

            $connections[] = $mockConnection;
        }

        $mockConnection = m::mock('\Elasticsearch\Connections\Connection')
                          ->shouldReceive('ping')->andReturn(true)->getMock()
                          ->shouldReceive('isAlive')->andReturn(true)->getMock()
                          ->shouldReceive('sniff')->andReturn($clusterState)->getMock()
                          ->shouldReceive('getTransportSchema')->once()->andReturn('http')->getMock()
                          ->shouldReceive('sniff')->andThrow('Elasticsearch\Common\Exceptions\Curl\OperationTimeoutException')->getMock();

        $connections[] = $mockConnection;

        $newConnections = $connections;
        $newConnections[] = m::mock('\Elasticsearch\Connections\Connection')
                            ->shouldReceive('isAlive')->andReturn(false)->getMock()
                            ->shouldReceive('ping')->andReturn(false)->getMock()
                            ->shouldReceive('sniff')->andThrow('Elasticsearch\Common\Exceptions\Curl\OperationTimeoutException')->getMock();

        $newConnections[] = m::mock('\Elasticsearch\Connections\Connection')
                            ->shouldReceive('isAlive')->andReturn(false)->getMock()
                            ->shouldReceive('ping')->andReturn(false)->getMock()
                            ->shouldReceive('sniff')->andThrow('Elasticsearch\Common\Exceptions\Curl\OperationTimeoutException')->getMock();

        $selector = m::mock('\Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector')
                    ->shouldReceive('select')
                    ->andReturnValues($newConnections)
                    ->getMock();

        $RRConnections = $newConnections;
        //array_push($connections);
        $connectionFactory = m::mock('\Elasticsearch\Connections\ConnectionFactory')
                             ->shouldReceive('create')->with(array('host' => '192.168.1.119', 'port' => 9200))->andReturn($newConnections[10])->getMock()
                             ->shouldReceive('create')->with(array('host' => '192.168.1.119', 'port' => 9201))->andReturn($newConnections[11])->getMock();

        $connectionPoolParams = array(
            'randomizeHosts' => false,
            'sniffingInterval'  => -1
        );
        $connectionPool = new SniffingConnectionPool($connections, $selector, $connectionFactory, $connectionPoolParams);

        $retConnection = $connectionPool->nextConnection();
        $this->assertEquals($newConnections[11], $retConnection);

        $retConnection = $connectionPool->nextConnection();
        $this->assertEquals($newConnections[12], $retConnection);
    }
}
