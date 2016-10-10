<?php

namespace Elasticsearch\Endpoints\Cluster\Nodes;

/**
 * Class Shutdown
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Cluster\Nodes
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class Shutdown extends AbstractNodesEndpoint
{
    /**
     * @return string
     */
    protected function getURI()
    {
        $node_id = $this->nodeID;
        $uri   = "/_shutdown";

        if (isset($node_id) === true) {
            $uri = "/_cluster/nodes/$node_id/_shutdown";
        }

        return $uri;
    }

    /**
     * @return string[]
     */
    protected function getParamWhitelist()
    {
        return array(
            'delay',
            'exit',
        );
    }

    /**
     * @return string
     */
    protected function getMethod()
    {
        return 'POST';
    }
}
