<?php

namespace Elasticsearch\Namespaces;

/**
 * Class ClusterNamespace
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Namespaces\ClusterNamespace
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class ClusterNamespace extends AbstractNamespace
{
    /**
     * $params['index']                      = (string) Limit the information returned to a specific index
     *        ['level']                      = (enum) Specify the level of detail for returned information
     *        ['local']                      = (boolean) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout']             = (time) Explicit operation timeout for connection to master node
     *        ['timeout']                    = (time) Explicit operation timeout
     *        ['wait_for_active_shards']     = (number) Wait until the specified number of shards is active
     *        ['wait_for_nodes']             = (number) Wait until the specified number of nodes is available
     *        ['wait_for_relocating_shards'] = (number) Wait until the specified number of relocating shards is finished
     *        ['wait_for_status']            = (enum) Wait until cluster is in a specific state
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function health($params = array())
    {
        $index = $this->extractArgument($params, 'index');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cluster\Health $endpoint */
        $endpoint = $endpointBuilder('Cluster\Health');
        $endpoint->setIndex($index);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['dry_run']         = (boolean) Simulate the operation only and return the resulting state
     *        ['filter_metadata'] = (boolean) Don't return cluster state metadata (default: false)
     *        ['body']            = (boolean) Don't return cluster state metadata (default: false)
     *        ['explain']         = (boolean) Return an explanation of why the commands can or cannot be executed
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function reroute($params = array())
    {
        $body = $this->extractArgument($params, 'body');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cluster\Reroute $endpoint */
        $endpoint = $endpointBuilder('Cluster\Reroute');
        $endpoint->setBody($body);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['filter_blocks']          = (boolean) Do not return information about blocks
     *        ['filter_index_templates'] = (boolean) Do not return information about index templates
     *        ['filter_indices']         = (list) Limit returned metadata information to specific indices
     *        ['filter_metadata']        = (boolean) Do not return information about indices metadata
     *        ['filter_nodes']           = (boolean) Do not return information about nodes
     *        ['filter_routing_table']   = (boolean) Do not return information about shard allocation (`routing_table` and `routing_nodes`)
     *        ['local']                  = (boolean) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout']         = (time) Specify timeout for connection to master
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function state($params = array())
    {
        $index = $this->extractArgument($params, 'index');
        $metric = $this->extractArgument($params, 'metric');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cluster\State $endpoint */
        $endpoint = $endpointBuilder('Cluster\State');
        $endpoint->setParams($params)
                 ->setIndex($index)
                 ->setMetric($metric);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['flat_settings']          = (boolean) Return settings in flat format (default: false)
     *        ['human'] = (boolean) Whether to return time and byte values in human-readable format.
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function stats($params = array())
    {
        $nodeID = $this->extractArgument($params, 'node_id');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cluster\Stats $endpoint */
        $endpoint = $endpointBuilder('Cluster\Stats');
        $endpoint->setNodeID($nodeID)
                 ->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['body'] = ()
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function putSettings($params = array())
    {
        $body = $this->extractArgument($params, 'body');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cluster\Settings\Put $endpoint */
        $endpoint = $endpointBuilder('Cluster\Settings\Put');
        $endpoint->setBody($body);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getSettings($params = array())
    {
        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cluster\Settings\Put $endpoint */
        $endpoint = $endpointBuilder('Cluster\Settings\Get');
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['local']   = (bool) Return local information, do not retrieve the state from master node (default: false)
     *        ['master_timeout']  = (time) Specify timeout for connection to master
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function pendingTasks($params = array())
    {
        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cluster\PendingTasks $endpoint */
        $endpoint = $endpointBuilder('Cluster\PendingTasks');
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }
}
