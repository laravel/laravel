<?php
use Elasticsearch\ClientBuilder;

/**
 * Class SniffingConnectionPoolIntegrationTest
 *
 * @category   Tests
 * @package    Elasticsearch
 * @subpackage Tests/SniffingConnectionPoolTest
 * @author     Zachary Tong <zachary.tong@elasticsearch.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link       http://elasticsearch.org
 */
class SniffingConnectionPoolIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testSniff()
    {
        $client = ClientBuilder::create()
            ->setHosts([$_SERVER['ES_TEST_HOST']])
            ->setConnectionPool('\Elasticsearch\ConnectionPool\SniffingConnectionPool', ['sniffingInterval' => -10])
            ->build();

        $client->ping();
    }
}
