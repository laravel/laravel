<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Query\Span;

use ONGR\ElasticsearchDSL\ParametersTrait;

/**
 * Elasticsearch span or query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-or-query.html
 */
class SpanOrQuery implements SpanQueryInterface
{
    use ParametersTrait;

    /**
     * @var SpanQueryInterface[]
     */
    private $queries = [];

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->setParameters($parameters);
    }

    /**
     * Add span query.
     *
     * @param SpanQueryInterface $query
     *
     * @return $this
     */
    public function addQuery(SpanQueryInterface $query)
    {
        $this->queries[] = $query;

        return $this;
    }

    /**
     * @return SpanQueryInterface[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'span_or';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $query = [];
        foreach ($this->queries as $type) {
            $query['clauses'][] = $type->toArray();
        }
        $output = $this->processArray($query);

        return [$this->getType() => $output];
    }
}
