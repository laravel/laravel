<?php

namespace Elasticsearch\Endpoints\Cluster\Nodes;

/**
 * Class Info
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Cluster\Nodes
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class Info extends AbstractNodesEndpoint
{
    // A comma-separated list of metrics you wish returned. Leave empty to return all.
    private $metric;

    /**
     * @param $metric
     *
     * @return $this
     */
    public function setMetric($metric)
    {
        if (isset($metric) !== true) {
            return $this;
        }

        if (is_array($metric) === true) {
            $metric = implode(",", $metric);
        }

        $this->metric = $metric;

        return $this;
    }

    /**
     * @return string
     */
    protected function getURI()
    {
        $node_id = $this->nodeID;
        $metric = $this->metric;
        $uri   = "/_nodes";

        if (isset($node_id) === true && isset($metric) === true) {
            $uri = "/_nodes/$node_id/$metric";
        } elseif (isset($metric) === true) {
            $uri = "/_nodes/$metric";
        } elseif (isset($node_id) === true) {
            $uri = "/_nodes/$node_id";
        }

        return $uri;
    }

    /**
     * @return string[]
     */
    protected function getParamWhitelist()
    {
        return array(
            'flat_settings',
            'human',
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
