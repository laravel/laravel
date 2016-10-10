<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Formatter\ElasticaFormatter;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\TestCase;
use Monolog\Logger;
use Elastica\Client;
use Elastica\Request;
use Elastica\Response;

class ElasticSearchHandlerTest extends TestCase
{
    /**
     * @var Client mock
     */
    protected $client;

    /**
     * @var array Default handler options
     */
    protected $options = array(
        'index' => 'my_index',
        'type'  => 'doc_type',
    );

    public function setUp()
    {
        // Elastica lib required
        if (!class_exists("Elastica\Client")) {
            $this->markTestSkipped("ruflin/elastica not installed");
        }

        // base mock Elastica Client object
        $this->client = $this->getMockBuilder('Elastica\Client')
            ->setMethods(array('addDocuments'))
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @covers Monolog\Handler\ElasticSearchHandler::write
     * @covers Monolog\Handler\ElasticSearchHandler::handleBatch
     * @covers Monolog\Handler\ElasticSearchHandler::bulkSend
     * @covers Monolog\Handler\ElasticSearchHandler::getDefaultFormatter
     */
    public function testHandle()
    {
        // log message
        $msg = array(
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('foo' => 7, 'bar', 'class' => new \stdClass),
            'datetime' => new \DateTime("@0"),
            'extra' => array(),
            'message' => 'log',
        );

        // format expected result
        $formatter = new ElasticaFormatter($this->options['index'], $this->options['type']);
        $expected = array($formatter->format($msg));

        // setup ES client mock
        $this->client->expects($this->any())
            ->method('addDocuments')
            ->with($expected);

        // perform tests
        $handler = new ElasticSearchHandler($this->client, $this->options);
        $handler->handle($msg);
        $handler->handleBatch(array($msg));
    }

    /**
     * @covers Monolog\Handler\ElasticSearchHandler::setFormatter
     */
    public function testSetFormatter()
    {
        $handler = new ElasticSearchHandler($this->client);
        $formatter = new ElasticaFormatter('index_new', 'type_new');
        $handler->setFormatter($formatter);
        $this->assertInstanceOf('Monolog\Formatter\ElasticaFormatter', $handler->getFormatter());
        $this->assertEquals('index_new', $handler->getFormatter()->getIndex());
        $this->assertEquals('type_new', $handler->getFormatter()->getType());
    }

    /**
     * @covers                   Monolog\Handler\ElasticSearchHandler::setFormatter
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage ElasticSearchHandler is only compatible with ElasticaFormatter
     */
    public function testSetFormatterInvalid()
    {
        $handler = new ElasticSearchHandler($this->client);
        $formatter = new NormalizerFormatter();
        $handler->setFormatter($formatter);
    }

    /**
     * @covers Monolog\Handler\ElasticSearchHandler::__construct
     * @covers Monolog\Handler\ElasticSearchHandler::getOptions
     */
    public function testOptions()
    {
        $expected = array(
            'index' => $this->options['index'],
            'type' => $this->options['type'],
            'ignore_error' => false,
        );
        $handler = new ElasticSearchHandler($this->client, $this->options);
        $this->assertEquals($expected, $handler->getOptions());
    }

    /**
     * @covers       Monolog\Handler\ElasticSearchHandler::bulkSend
     * @dataProvider providerTestConnectionErrors
     */
    public function testConnectionErrors($ignore, $expectedError)
    {
        $clientOpts = array('host' => '127.0.0.1', 'port' => 1);
        $client = new Client($clientOpts);
        $handlerOpts = array('ignore_error' => $ignore);
        $handler = new ElasticSearchHandler($client, $handlerOpts);

        if ($expectedError) {
            $this->setExpectedException($expectedError[0], $expectedError[1]);
            $handler->handle($this->getRecord());
        } else {
            $this->assertFalse($handler->handle($this->getRecord()));
        }
    }

    /**
     * @return array
     */
    public function providerTestConnectionErrors()
    {
        return array(
            array(false, array('RuntimeException', 'Error sending messages to Elasticsearch')),
            array(true, false),
        );
    }

    /**
     * Integration test using localhost Elastic Search server
     *
     * @covers Monolog\Handler\ElasticSearchHandler::__construct
     * @covers Monolog\Handler\ElasticSearchHandler::handleBatch
     * @covers Monolog\Handler\ElasticSearchHandler::bulkSend
     * @covers Monolog\Handler\ElasticSearchHandler::getDefaultFormatter
     */
    public function testHandleIntegration()
    {
        $msg = array(
            'level' => Logger::ERROR,
            'level_name' => 'ERROR',
            'channel' => 'meh',
            'context' => array('foo' => 7, 'bar', 'class' => new \stdClass),
            'datetime' => new \DateTime("@0"),
            'extra' => array(),
            'message' => 'log',
        );

        $expected = $msg;
        $expected['datetime'] = $msg['datetime']->format(\DateTime::ISO8601);
        $expected['context'] = array(
            'class' => '[object] (stdClass: {})',
            'foo' => 7,
            0 => 'bar',
        );

        $client = new Client();
        $handler = new ElasticSearchHandler($client, $this->options);
        try {
            $handler->handleBatch(array($msg));
        } catch (\RuntimeException $e) {
            $this->markTestSkipped("Cannot connect to Elastic Search server on localhost");
        }

        // check document id from ES server response
        $documentId = $this->getCreatedDocId($client->getLastResponse());
        $this->assertNotEmpty($documentId, 'No elastic document id received');

        // retrieve document source from ES and validate
        $document = $this->getDocSourceFromElastic(
            $client,
            $this->options['index'],
            $this->options['type'],
            $documentId
        );
        $this->assertEquals($expected, $document);

        // remove test index from ES
        $client->request("/{$this->options['index']}", Request::DELETE);
    }

    /**
     * Return last created document id from ES response
     * @param  Response    $response Elastica Response object
     * @return string|null
     */
    protected function getCreatedDocId(Response $response)
    {
        $data = $response->getData();
        if (!empty($data['items'][0]['create']['_id'])) {
            return $data['items'][0]['create']['_id'];
        }
    }

    /**
     * Retrieve document by id from Elasticsearch
     * @param  Client $client     Elastica client
     * @param  string $index
     * @param  string $type
     * @param  string $documentId
     * @return array
     */
    protected function getDocSourceFromElastic(Client $client, $index, $type, $documentId)
    {
        $resp = $client->request("/{$index}/{$type}/{$documentId}", Request::GET);
        $data = $resp->getData();
        if (!empty($data['_source'])) {
            return $data['_source'];
        }

        return array();
    }
}
