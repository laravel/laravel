<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Query;

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\ParametersTrait;

/**
 * Represents Elasticsearch "geo_distance_range" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-range-query.html
 */
class GeoDistanceRangeQuery implements BuilderInterface
{
    use ParametersTrait;

    /**
     * @var string
     */
    private $field;

    /**
     * @var array
     */
    private $range;

    /**
     * @var mixed
     */
    private $location;

    /**
     * @param string $field
     * @param array  $range
     * @param mixed  $location
     * @param array  $parameters
     */
    public function __construct($field, $range, $location, array $parameters = [])
    {
        $this->field = $field;
        $this->range = $range;
        $this->location = $location;

        $this->setParameters($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'geo_distance_range';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $query = $this->range + [$this->field => $this->location];
        $output = $this->processArray($query);

        return [$this->getType() => $output];
    }
}
