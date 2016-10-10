<?php

namespace Elasticsearch\Tests;

use Elasticsearch;
use Elasticsearch\ClientBuilder;
use Mockery as m;

/**
 * Class ClientTest
 *
 * @category   Tests
 * @package    Elasticsearch
 * @subpackage Tests
 * @author     Zachary Tong <zachary.tong@elasticsearch.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link       http://elasticsearch.org
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @expectedException \Elasticsearch\Common\Exceptions\InvalidArgumentException
     */
    public function testConstructorIllegalPort()
    {
        $client = Elasticsearch\ClientBuilder::create()->setHosts(['localhost:abc'])->build();
    }

    public function testCustomQueryParams()
    {
        $params = array();

        $client = Elasticsearch\ClientBuilder::create()->setHosts([$_SERVER['ES_TEST_HOST']])->build();

        $getParams = array(
            'index' => 'test',
            'type' => 'test',
            'id' => 1,
            'parent' => 'abc',
            'custom' => array('customToken' => 'abc', 'otherToken' => 123)
        );
        $exists = $client->exists($getParams);
    }

    public function testFromConfig()
    {
        $params = [
            'hosts' => [
                'localhost:9200'
            ],
            'retries' => 2,
            'handler' => ClientBuilder::multiHandler()
        ];
        $client = ClientBuilder::fromConfig($params);
    }

    /**
     * @expectedException \Elasticsearch\Common\Exceptions\RuntimeException
     */
    public function testFromConfigBadParam()
    {
        $params = [
            'hosts' => [
                'localhost:9200'
            ],
            'retries' => 2,
            'imNotReal' => 5
        ];
        $client = ClientBuilder::fromConfig($params);
    }

    public function testFromConfigBadParamQuiet()
    {
        $params = [
            'hosts' => [
                'localhost:9200'
            ],
            'retries' => 2,
            'imNotReal' => 5
        ];
        $client = ClientBuilder::fromConfig($params, true);
    }

    public function testNullDelete()
    {
        $client = ClientBuilder::create()->build();

        try {
            $client->delete([
                'index' => null,
                'type' => 'test',
                'id' => 'test'
            ]);
            $this->fail("InvalidArgumentException was not thrown");
        } catch (Elasticsearch\Common\Exceptions\InvalidArgumentException $e) {
            // all good
        }

        try {
            $client->delete([
                'index' => 'test',
                'type' => null,
                'id' => 'test'
            ]);
            $this->fail("InvalidArgumentException was not thrown");
        } catch (Elasticsearch\Common\Exceptions\InvalidArgumentException $e) {
            // all good
        }

        try {
            $client->delete([
                'index' => 'test',
                'type' => 'test',
                'id' => null
            ]);
            $this->fail("InvalidArgumentException was not thrown");
        } catch (Elasticsearch\Common\Exceptions\InvalidArgumentException $e) {
            // all good
        }
    }

    public function testEmptyStringDelete()
    {
        $client = ClientBuilder::create()->build();

        try {
            $client->delete([
                'index' => '',
                'type' => 'test',
                'id' => 'test'
            ]);
            $this->fail("InvalidArgumentException was not thrown");
        } catch (Elasticsearch\Common\Exceptions\InvalidArgumentException $e) {
            // all good
        }

        try {
            $client->delete([
                'index' => 'test',
                'type' => '',
                'id' => 'test'
            ]);
            $this->fail("InvalidArgumentException was not thrown");
        } catch (Elasticsearch\Common\Exceptions\InvalidArgumentException $e) {
            // all good
        }

        try {
            $client->delete([
                'index' => 'test',
                'type' => 'test',
                'id' => ''
            ]);
            $this->fail("InvalidArgumentException was not thrown");
        } catch (Elasticsearch\Common\Exceptions\InvalidArgumentException $e) {
            // all good
        }
    }

    public function testArrayOfEmptyStringDelete()
    {
        $client = ClientBuilder::create()->build();

        try {
            $client->delete([
                'index' => ['','',''],
                'type' => 'test',
                'id' => 'test'
            ]);
            $this->fail("InvalidArgumentException was not thrown");
        } catch (Elasticsearch\Common\Exceptions\InvalidArgumentException $e) {
            // all good
        }

        try {
            $client->delete([
                'index' => 'test',
                'type' => ['','',''],
                'id' => 'test'
            ]);
            $this->fail("InvalidArgumentException was not thrown");
        } catch (Elasticsearch\Common\Exceptions\InvalidArgumentException $e) {
            // all good
        }
    }

    public function testArrayOfNullDelete()
    {
        $client = ClientBuilder::create()->build();

        try {
            $client->delete([
                'index' => [null, null, null],
                'type' => 'test',
                'id' => 'test'
            ]);
            $this->fail("InvalidArgumentException was not thrown");
        } catch (Elasticsearch\Common\Exceptions\InvalidArgumentException $e) {
            // all good
        }

        try {
            $client->delete([
                'index' => 'test',
                'type' => [null, null, null],
                'id' => 'test'
            ]);
            $this->fail("InvalidArgumentException was not thrown");
        } catch (Elasticsearch\Common\Exceptions\InvalidArgumentException $e) {
            // all good
        }
    }

    public function testMaxRetriesException()
    {
        $client = Elasticsearch\ClientBuilder::create()
            ->setHosts(["localhost:1"])
            ->setRetries(0)
            ->build();

        $searchParams = array(
            'index' => 'test',
            'type' => 'test',
            'body' => [
                'query' => [
                    'match_all' => []
                ]
            ]
        );

        $client = Elasticsearch\ClientBuilder::create()
            ->setHosts(["localhost:1"])
            ->setRetries(0)
            ->build();

        try {
            $client->search($searchParams);
            $this->fail("Should have thrown CouldNotConnectToHost");
        } catch (Elasticsearch\Common\Exceptions\Curl\CouldNotConnectToHost $e) {
            // All good
            $previous = $e->getPrevious();
            $this->assertInstanceOf('Elasticsearch\Common\Exceptions\MaxRetriesException', $previous);
        } catch (\Exception $e) {
            throw $e;
        }


        $client = Elasticsearch\ClientBuilder::create()
            ->setHosts(["localhost:1"])
            ->setRetries(0)
            ->build();

        try {
            $client->search($searchParams);
            $this->fail("Should have thrown TransportException");
        } catch (Elasticsearch\Common\Exceptions\TransportException $e) {
            // All good
            $previous = $e->getPrevious();
            $this->assertInstanceOf('Elasticsearch\Common\Exceptions\MaxRetriesException', $previous);
        } catch (\Exception $e) {
            throw $e;
        }
    }


}
