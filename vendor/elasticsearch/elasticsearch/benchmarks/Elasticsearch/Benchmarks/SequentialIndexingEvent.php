<?php

namespace Elasticsearch\Benchmarks;

use Athletic\AthleticEvent;
use Elasticsearch\ClientBuilder;

class SequentialIndexingEvent extends AthleticEvent
{
    /** @var  Client */
    private $setupClient;

    /** @var  Client */
    private $client;

    private $document;
    private $largeDocument;
    private $mediumDocument;

    protected function classSetUp()
    {
        $this->client = $client = ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();

        $this->setupClient = $client = ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
        $indexParams['index']  = 'benchmarking_index';
        $indexParams['body']['test']['_all']['enabled'] = false;
        $indexParams['body']['test']['properties']['testField'] = array(
            'type' => 'string',
            'store' => 'no',
            'index' => 'no'
        );

        $this->setupClient->indices()->create($indexParams);

        $this->document = array();
        $this->document['body']  = array('testField' => 'abc');
        $this->document['index'] = 'benchmarking_index';
        $this->document['type']  = 'test';

        $this->mediumDocument = array();
        $this->mediumDocument['body']['testField'] = str_repeat('a', 1000);
        $this->mediumDocument['index']             = 'benchmarking_index';
        $this->mediumDocument['type']              = 'test';

        $this->largeDocument = array();
        $this->largeDocument['body']['testField'] = str_repeat('a', 5000);
        $this->largeDocument['index']             = 'benchmarking_index';
        $this->largeDocument['type']              = 'test';
    }

    protected function classTearDown()
    {
        $indexParams['index']  = 'benchmarking_index';
        $this->setupClient->indices()->delete($indexParams);
    }

    /**
     * @iterations 1000
     * @group small
     * @baseline
     */
    public function syncSmall()
    {
        $response = $this->client->index($this->document);
        $response = $response['created'];
    }

    /**
     * @iterations 1000
     * @group medium
     * @baseline
     */
    public function syncMedium()
    {
        $response = $this->client->index($this->mediumDocument);
        $response = $response['created'];
    }

    /**
     * @iterations 1000
     * @group large
     * @baseline
     */
    public function syncLarge()
    {
        $response = $this->client->index($this->largeDocument);
        $response = $response['created'];
    }

    /**
     * @iterations 1000
     * @group small
     */
    public function asyncSmall()
    {
        $asyncDoc = $this->document;
        $asyncDoc['client']['future'] = 'lazy';
        $response = $this->client->index($asyncDoc);
        $response = $response['body']['created'];
    }

    /**
     * @iterations 1000
     * @group medium
     */
    public function asyncMedium()
    {
        $asyncDoc = $this->mediumDocument;
        $asyncDoc['client']['future'] = 'lazy';
        $response = $this->client->index($asyncDoc);
        $response = $response['body']['created'];
    }

    /**
     * @iterations 1000
     * @group large
     */
    public function asyncLarge()
    {
        $asyncDoc = $this->largeDocument;
        $asyncDoc['client']['future'] = 'lazy';
        $response = $this->client->index($asyncDoc);
        $response = $response['body']['created'];
    }
}
