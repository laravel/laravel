<?php

namespace Elasticsearch\Serializers;

/**
 * Interface SerializerInterface
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Serializers
 * @author   Zachary Tong <zachary.tong@elasticsearch.com>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elasticsearch.org
 */
interface SerializerInterface
{
    /**
     * Serialize a complex data-structure into a json encoded string
     *
     * @param mixed   The data to encode
     *
     * @return string
     */
    public function serialize($data);

    /**
     * Deserialize json encoded string into an associative array
     *
     * @param string $data    JSON encoded string
     * @param array  $headers Response Headers
     *
     * @return array
     */
    public function deserialize($data, $headers);
}
