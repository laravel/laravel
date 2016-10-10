<?php

namespace Elasticsearch\Tests\ConnectionPool\Selectors;

use Elasticsearch;

/**
 * Class SnifferTest
 *
 * @category   Tests
 * @package    Elasticsearch
 * @subpackage Tests\ConnectionPool\RoundRobinSelectorTest
 * @author     Zachary Tong <zachary.tong@elasticsearch.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link       http://elasticsearch.org
 */
class RoundRobinSelectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Add Ten connections, select 15 to verify round robin
     *
     * @covers \Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector::select
     *
     * @return void
     */
    public function testTenConnections()
    {
        $roundRobin = new Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector();

        $mockConnections = array();
        foreach (range(0, 10) as $index) {
            $mockConnections[$index] = $this->getMockBuilder('\Elasticsearch\Connections\CurlMultiConnection')
                ->disableOriginalConstructor()
                ->getMock();
        }

        foreach (range(0, 15) as $index) {
            $retConnection = $roundRobin->select($mockConnections);

            $nextIndex = ($index % 10) + 1;
            $this->assertEquals($mockConnections[$nextIndex], $retConnection);
        }
    }

    /**
     * Add Ten connections, select five, remove thre, test another 10 to check
     * that the round-robining works after removing connections
     *
     * @covers \Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector::select
     *
     * @return void
     */
    public function testAddTenConnectionsestFiveTRemoveThree()
    {
        $roundRobin = new Elasticsearch\ConnectionPool\Selectors\RoundRobinSelector();

        $mockConnections = array();
        foreach (range(0, 10) as $index) {
            $mockConnections[$index] = $this->getMockBuilder('\Elasticsearch\Connections\CurlMultiConnection')
                ->disableOriginalConstructor()
                ->getMock();
        }

        foreach (range(0, 4) as $index) {
            $retConnection = $roundRobin->select($mockConnections);

            $nextIndex = ($index % (count($mockConnections)-1)) + 1;
            $this->assertEquals($mockConnections[$nextIndex], $retConnection);
        }

        unset($mockConnections[8]);
        unset($mockConnections[9]);
        unset($mockConnections[10]);

        foreach (range(5, 15) as $index) {
            $retConnection = $roundRobin->select($mockConnections);

            $nextIndex = ($index % (count($mockConnections)-1)) + 1;
            $this->assertEquals($mockConnections[$nextIndex], $retConnection);
        }
    }
}
