<?php

namespace Elasticsearch\Tests\ConnectionPool\Selectors;

use Elasticsearch;
use Mockery as m;

/**
 * Class StickyRoundRobinSelectorTest
 *
 * @category   Tests
 * @package    Elasticsearch
 * @subpackage Tests\ConnectionPool\StickyRoundRobinSelectorTest
 * @author     Zachary Tong <zachary.tong@elasticsearch.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link       http://elasticsearch.org
 */
class StickyRoundRobinSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testTenConnections()
    {
        $roundRobin = new Elasticsearch\ConnectionPool\Selectors\StickyRoundRobinSelector();

        $mockConnections = array();
        $mockConnections[] = m::mock('\Elasticsearch\Connections\GuzzleConnection')
                             ->shouldReceive('isAlive')->times(16)->andReturn(true)->getMock();

        foreach (range(0, 9) as $index) {
            $mockConnections[] = m::mock('\Elasticsearch\Connections\GuzzleConnection');
        }

        foreach (range(0, 15) as $index) {
            $retConnection = $roundRobin->select($mockConnections);

            $this->assertEquals($mockConnections[0], $retConnection);
        }
    }

    public function testTenConnectionsFirstDies()
    {
        $roundRobin = new Elasticsearch\ConnectionPool\Selectors\StickyRoundRobinSelector();

        $mockConnections = array();
        $mockConnections[] = m::mock('\Elasticsearch\Connections\GuzzleConnection')
                             ->shouldReceive('isAlive')->once()->andReturn(false)->getMock();

        $mockConnections[] = m::mock('\Elasticsearch\Connections\GuzzleConnection')
                             ->shouldReceive('isAlive')->times(15)->andReturn(true)->getMock();

        foreach (range(0, 8) as $index) {
            $mockConnections[] = m::mock('\Elasticsearch\Connections\GuzzleConnection');
        }

        foreach (range(0, 15) as $index) {
            $retConnection = $roundRobin->select($mockConnections);

            $this->assertEquals($mockConnections[1], $retConnection);
        }
    }
}
