<?php

namespace Elasticsearch\Endpoints\Indices\Warmer;

use Elasticsearch\Endpoints\AbstractEndpoint;
use Elasticsearch\Common\Exceptions;

/**
 * Class Get
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Indices\Warmer
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class Get extends AbstractEndpoint
{
    // The name of the warmer (supports wildcards); leave empty to get all warmers
    private $name;

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        if (isset($name) !== true) {
            return $this;
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @throws \Elasticsearch\Common\Exceptions\RuntimeException
     * @return string
     */
    protected function getURI()
    {
        $index = $this->index;
        $name = $this->name;
        $type = $this->type;
        $uri   = "/_warmer";

        if (isset($index) === true && isset($type) === true && isset($name) === true) {
            $uri = "/$index/$type/_warmer/$name";
        } elseif (isset($index) === true && isset($name) === true) {
            $uri = "/$index/_warmer/$name";
        } elseif (isset($index) === true && isset($type) === true) {
            throw new Exceptions\RuntimeException('Invalid index/type/name combination. If index + type are defined, name must also be defined');
        } elseif (isset($index) === true) {
            $uri = "/$index/_warmer";
        } elseif (isset($name) === true) {
            $uri = "/_warmer/$name";
        }

        return $uri;
    }

    /**
     * @return string[]
     */
    protected function getParamWhitelist()
    {
        return array(
            'ignore_unavailable',
            'allow_no_indices',
            'expand_wildcards',
            'local',
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
