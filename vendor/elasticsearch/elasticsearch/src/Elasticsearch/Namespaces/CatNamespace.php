<?php

namespace Elasticsearch\Namespaces;

/**
 * Class CatNamespace
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Namespaces\CatNamespace
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class CatNamespace extends AbstractNamespace
{
    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function aliases($params = array())
    {
        $name = $this->extractArgument($params, 'name');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Aliases $endpoint */
        $endpoint = $endpointBuilder('Cat\Aliases');
        $endpoint->setName($name);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *        ['bytes']          = (enum) The unit in which to display byte values
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function allocation($params = array())
    {
        $nodeID = $this->extractArgument($params, 'node_id');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Allocation $endpoint */
        $endpoint = $endpointBuilder('Cat\Allocation');
        $endpoint->setNodeID($nodeID);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function count($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Count $endpoint */
        $endpoint = $endpointBuilder('Cat\Count');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *        ['ts']             = (bool) Set to false to disable timestamping
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function health($params = array())
    {
        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Health $endpoint */
        $endpoint = $endpointBuilder('Cat\Health');
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['help'] = (bool) Return help information
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function help($params = array())
    {
        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Help $endpoint */
        $endpoint = $endpointBuilder('Cat\Help');
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *        ['bytes']          = (enum) The unit in which to display byte values
     *        ['pri']            = (bool) Set to true to return stats only for primary shards
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function indices($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Indices $endpoint */
        $endpoint = $endpointBuilder('Cat\Indices');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function master($params = array())
    {
        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Master $endpoint */
        $endpoint = $endpointBuilder('Cat\Master');
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function nodes($params = array())
    {
        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Nodes $endpoint */
        $endpoint = $endpointBuilder('Cat\Nodes');
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function nodeAttrs($params = array())
    {
        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\NodeAttrs $endpoint */
        $endpoint = $endpointBuilder('Cat\NodeAttrs');
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function pendingTasks($params = array())
    {
        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\PendingTasks $endpoint */
        $endpoint = $endpointBuilder('Cat\PendingTasks');
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *        ['bytes']          = (enum) The unit in which to display byte values
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function recovery($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Recovery $endpoint */
        $endpoint = $endpointBuilder('Cat\Recovery');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function repositories($params = array())
    {
        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Repositories $endpoint */
        $endpoint = $endpointBuilder('Cat\Repositories');
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *        ['bytes']          = (enum) The unit in which to display byte values
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function shards($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Shards $endpoint */
        $endpoint = $endpointBuilder('Cat\Shards');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *        ['bytes']          = (enum) The unit in which to display byte values
     *        ['repository']     = (string) Name of repository from which to fetch the snapshot information
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function snapshots($params = array())
    {
        $repository = $this->extractArgument($params, 'repository');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Snapshots $endpoint */
        $endpoint = $endpointBuilder('Cat\Snapshots');
        $endpoint->setRepository($repository);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *        ['full_id']        = (bool) Enables displaying the complete node ids
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function threadPool($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\ThreadPool $endpoint */
        $endpoint = $endpointBuilder('Cat\ThreadPool');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *        ['bytes']          = (enum) The unit in which to display byte values
     *        ['fields']         = (list) A comma-separated list of fields to return the fielddata size
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function fielddata($params = array())
    {
        $fields = $this->extractArgument($params, 'fields');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Fielddata $endpoint */
        $endpoint = $endpointBuilder('Cat\Fielddata');
        $endpoint->setFields($fields);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']          = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout'] = (time) Explicit operation timeout for connection to master node
     *        ['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function plugins($params = array())
    {
        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Plugins $endpoint */
        $endpoint = $endpointBuilder('Cat\Plugins');
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['h']              = (list) Comma-separated list of column names to display
     *        ['help']           = (bool) Return help information
     *        ['v']              = (bool) Verbose mode. Display column headers
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function segments($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cat\Segments $endpoint */
        $endpoint = $endpointBuilder('Cat\Segments');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }
}
