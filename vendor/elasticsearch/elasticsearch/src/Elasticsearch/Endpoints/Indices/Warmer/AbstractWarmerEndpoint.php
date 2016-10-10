<?php

namespace Elasticsearch\Endpoints\Indices\Warmer;

use Elasticsearch\Common\Exceptions\RuntimeException;
use Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class AbstractWarmerEndpoint
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Indices\Type
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
abstract class AbstractWarmerEndpoint extends AbstractEndpoint
{
    /** @var  string */
    protected $name;

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     * @throws \Elasticsearch\Common\Exceptions\RuntimeException
     */
    protected function getWarmerURI()
    {
        if (isset($this->index) !== true) {
            throw new RuntimeException(
                'index is required for Delete'
            );
        }

        $uri = $this->getOptionalURI('_warmer');

        $name = $this->name;
        if (isset($name) === true) {
            $uri .= "/$name";
        }

        return $uri;
    }
}
