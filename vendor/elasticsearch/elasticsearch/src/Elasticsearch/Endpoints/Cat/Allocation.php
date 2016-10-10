<?php

namespace Elasticsearch\Endpoints\Cat;

use Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class Allocation
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Cat
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class Allocation extends AbstractEndpoint
{
    // A comma-separated list of node IDs or names to limit the returned information
    private $node_id;

    /**
     * @param $node_id
     *
     * @return $this
     */
    public function setNodeId($node_id)
    {
        if (isset($node_id) !== true) {
            return $this;
        }

        $this->node_id = $node_id;

        return $this;
    }

    /**
     * @return string
     */
    protected function getURI()
    {
        $node_id = $this->node_id;
        $uri   = "/_cat/allocation";

        if (isset($node_id) === true) {
            $uri = "/_cat/allocation/$node_id";
        }

        return $uri;
    }

    /**
     * @return string[]
     */
    protected function getParamWhitelist()
    {
        return array(
            'bytes',
            'local',
            'master_timeout',
            'h',
            'help',
            'v',
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
