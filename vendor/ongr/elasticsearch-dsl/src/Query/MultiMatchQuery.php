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
 * Represents Elasticsearch "multi_match" query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html
 */
class MultiMatchQuery implements BuilderInterface
{
    use ParametersTrait;

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var string
     */
    private $query;

    /**
     * @param array  $fields
     * @param string $query
     * @param array  $parameters
     */
    public function __construct(array $fields, $query, array $parameters = [])
    {
        $this->fields = $fields;
        $this->query = $query;
        $this->setParameters($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'multi_match';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $query = [
            'fields' => $this->fields,
            'query' => $this->query,
        ];

        $output = $this->processArray($query);

        return [$this->getType() => $output];
    }
}
