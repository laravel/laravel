<?php

namespace Elasticsearch\Namespaces;

/**
 * Class NodesNamespace
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Namespaces\NodesNamespace
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class NodesNamespace extends AbstractNamespace
{
    /**
     * $params['fields']        = (list) A comma-separated list of fields for `fielddata` metric (supports wildcards)
     *        ['metric_family'] = (enum) Limit the information returned to a certain metric family
     *        ['metric']        = (enum) Limit the information returned for `indices` family to a specific metric
     *        ['node_id']       = (list) A comma-separated list of node IDs or names to limit the returned information; use `_local` to return information from the node you're connecting to, leave empty to get information from all nodes
     *        ['all']           = (boolean) Return all available information
     *        ['clear']         = (boolean) Reset the default level of detail
     *        ['fs']            = (boolean) Return information about the filesystem
     *        ['http']          = (boolean) Return information about HTTP
     *        ['indices']       = (boolean) Return information about indices
     *        ['jvm']           = (boolean) Return information about the JVM
     *        ['network']       = (boolean) Return information about network
     *        ['os']            = (boolean) Return information about the operating system
     *        ['process']       = (boolean) Return information about the Elasticsearch process
     *        ['thread_pool']   = (boolean) Return information about the thread pool
     *        ['transport']     = (boolean) Return information about transport
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function stats($params = array())
    {
        $nodeID = $this->extractArgument($params, 'node_id');

        $metric = $this->extractArgument($params, 'metric');

        $index_metric = $this->extractArgument($params, 'index_metric');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cluster\Nodes\Stats $endpoint */
        $endpoint = $endpointBuilder('Cluster\Nodes\Stats');
        $endpoint->setNodeID($nodeID)
                 ->setMetric($metric)
                 ->setIndexMetric($index_metric)
                 ->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['node_id']       = (list) A comma-separated list of node IDs or names to limit the returned information; use `_local` to return information from the node you're connecting to, leave empty to get information from all nodes
     *        ['metric']        = (list) A comma-separated list of metrics you wish returned. Leave empty to return all.
     *        ['flat_settings'] = (boolean) Return settings in flat format (default: false)
     *        ['human']         = (boolean) Whether to return time and byte values in human-readable format.

     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function info($params = array())
    {
        $nodeID = $this->extractArgument($params, 'node_id');
        $metric = $this->extractArgument($params, 'metric');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cluster\Nodes\Info $endpoint */
        $endpoint = $endpointBuilder('Cluster\Nodes\Info');
        $endpoint->setNodeID($nodeID)->setMetric($metric);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['node_id']   = (list) A comma-separated list of node IDs or names to limit the returned information; use `_local` to return information from the node you're connecting to, leave empty to get information from all nodes
     *        ['interval']  = (time) The interval for the second sampling of threads
     *        ['snapshots'] = (number) Number of samples of thread stacktrace (default: 10)
     *        ['threads']   = (number) Specify the number of threads to provide information for (default: 3)
     *        ['type']      = (enum) The type to sample (default: cpu)
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function hotThreads($params = array())
    {
        $nodeID = $this->extractArgument($params, 'node_id');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cluster\Nodes\HotThreads $endpoint */
        $endpoint = $endpointBuilder('Cluster\Nodes\HotThreads');
        $endpoint->setNodeID($nodeID);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }

    /**
     * $params['node_id'] = (list) A comma-separated list of node IDs or names to perform the operation on; use `_local` to perform the operation on the node you're connected to, leave empty to perform the operation on all nodes
     *        ['delay']   = (time) Set the delay for the operation (default: 1s)
     *        ['exit']    = (boolean) Exit the JVM as well (default: true)
     *
     * @param $params array Associative array of parameters
     *
     * @return array
     */
    public function shutdown($params = array())
    {
        $nodeID = $this->extractArgument($params, 'node_id');

        /** @var callback $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var \Elasticsearch\Endpoints\Cluster\Nodes\Shutdown $endpoint */
        $endpoint = $endpointBuilder('Cluster\Nodes\Shutdown');
        $endpoint->setNodeID($nodeID);
        $endpoint->setParams($params);
        $response = $endpoint->performRequest();

        return $endpoint->resultOrFuture($response);
    }
}
