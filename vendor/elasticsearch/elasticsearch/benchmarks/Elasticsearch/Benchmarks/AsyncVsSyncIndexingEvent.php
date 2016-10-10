<?php

namespace Elasticsearch\Benchmarks;

use Athletic\AthleticEvent;
use Elasticsearch\ClientBuilder;

class AsyncVsSyncIndexingEvent extends AthleticEvent
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
     * @iterations 10
     * @group small
     * @baseline
     */
    public function syncSmall()
    {
        $responses = [];
        for ($i = 0; $i < 1000; $i++) {
            $responses[] = $this->client->index($this->document);
        }
    }

    /**
     * @iterations 10
     * @group medium
     * @baseline
     */
    public function syncMedium()
    {
        $responses = [];
        for ($i = 0; $i < 1000; $i++) {
            $responses[] = $this->client->index($this->mediumDocument);
        }
    }

    /**
     * @iterations 10
     * @group large
     * @baseline
     */
    public function syncLarge()
    {
        $responses = [];
        for ($i = 0; $i < 1000; $i++) {
            $responses[] = $this->client->index($this->largeDocument);
        }
    }

    /**
     * @iterations 10
     * @group small
     */
    public function asyncSmall()
    {
        $responses = [];
        $asyncDoc = $this->document;
        $asyncDoc['client']['future'] = 'lazy';

        for ($i = 0; $i < 1000; $i++) {
            $responses[] = $this->client->index($asyncDoc);
        }

        $responses[999]->wait();
    }

    /**
     * @iterations 10
     * @group medium
     */
    public function asyncMedium()
    {
        $responses = [];
        $asyncDoc = $this->mediumDocument;
        $asyncDoc['client']['future'] = 'lazy';

        for ($i = 0; $i < 1000; $i++) {
            $responses[] = $this->client->index($asyncDoc);
        }

        $responses[999]->wait();
    }

    /**
     * @iterations 10
     * @group large
     */
    public function asyncLarge()
    {
        $responses = [];
        $asyncDoc = $this->largeDocument;
        $asyncDoc['client']['future'] = 'lazy';

        for ($i = 0; $i < 1000; $i++) {
            $responses[] = $this->client->index($asyncDoc);
        }

        $responses[999]->wait();
    }
}
