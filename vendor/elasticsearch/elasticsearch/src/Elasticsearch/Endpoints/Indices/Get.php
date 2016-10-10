<?php

namespace Elasticsearch\Endpoints\Indices;

use Elasticsearch\Endpoints\AbstractEndpoint;
use Elasticsearch\Common\Exceptions;

/**
 * Class Get
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Get
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
class Get extends AbstractEndpoint
{
    private $feature;

    /**
     * @throws \Elasticsearch\Common\Exceptions\RuntimeException
     * @return string
     */
    protected function getURI()
    {
        if (isset($this->index) !== true) {
            throw new Exceptions\RuntimeException(
                'index is required for Get'
            );
        }
        $index   = $this->index;
        $feature = $this->feature;
        $uri     = "/$index";

        if (isset($feature) === true) {
            $uri = "/$index/$feature";
        }

        return $uri;
    }

    public function setFeature($feature)
    {
        if (isset($feature) !== true) {
            return $this;
        }

        if (is_array($feature) === true) {
            $feature = implode(",", $feature);
        }

        $this->feature = $feature;

        return $this;
    }

    /**
     * @return string[]
     */
    protected function getParamWhitelist()
    {
        return array(
            'local',
            'ignore_unavailable',
            'allow_no_indices',
            'expand_wildcards',
            'human'
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
