<?php

namespace Elasticsearch\Endpoints\Cluster\Nodes;

/**
 * Class Hotthreads
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Cluster\Nodes
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class HotThreads extends AbstractNodesEndpoint
{
    /**
     * @return string
     */
    protected function getURI()
    {
        $node_id = $this->nodeID;
        $uri   = "/_cluster/nodes/hotthreads";

        if (isset($node_id) === true) {
            $uri = "/_cluster/nodes/$node_id/hotthreads";
        }

        return $uri;
    }

    /**
     * @return string[]
     */
    protected function getParamWhitelist()
    {
        return array(
            'interval',
            'snapshots',
            'threads',
            'type',
        );
    }

    /**
     * @return string
     */
    protected function getMethod()
    {
        return 'GET';
    }
}
